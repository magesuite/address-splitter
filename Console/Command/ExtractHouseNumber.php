<?php

namespace MageSuite\AddressSplitter\Console\Command;

class ExtractHouseNumber extends \Symfony\Component\Console\Command\Command
{
    const MODE_PREVIEW = 'preview';
    const MODE_EXECUTE = 'execute';

    /**
     * @var \MageSuite\AddressSplitter\Model\Command\ExtractHouseNumberFactory
     */
    protected $extractHouserNumberFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    protected $allowedModes = [
        self::MODE_PREVIEW,
        self::MODE_EXECUTE
    ];

    protected $questions = [
        'limit' => [
            'question' => 'How many record should be in preview?',
            'default' => 100
        ],
        'random_rows' => [
            'question' => 'Get random records from database? [Y/n]',
            'default' => 'y',
        ],
        'confirm' => [
            'question' => 'Are parameters correct and you want to continue? [Y/n]',
            'default' => 'n'
        ],
        'confirm_execute' => [
            'question' => "This will update street in customer addresses in the database and will be very difficult to undo. \nYou need to know what are you doing. \nContinue? [Y/n]",
            'default' => 'n'
        ]
    ];

    public function __construct(
        \MageSuite\AddressSplitter\Model\Command\ExtractHouseNumberFactory $extractHouseNumberFactory,
        \Magento\Framework\App\State $state
    ) {
        parent::__construct();

        $this->extractHouserNumberFactory = $extractHouseNumberFactory;
        $this->state = $state;
    }

    protected function configure()
    {
        $this->addArgument(
            'mode',
            \Symfony\Component\Console\Input\InputArgument::REQUIRED,
            'Mode: preview or execute'
        );

        $this->setName('address:extract:housenumber')
            ->setDescription('Extract house number from address to separate column');
    }

    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);

        $mode = $input->getArgument('mode');

        if (empty($mode) || !in_array($mode, $this->allowedModes)) {
            $output->writeln('Wrong mode. Allowed modes: ' . implode(', ', $this->allowedModes));
            return false;
        }

        $dialog = $this->getHelperSet()->get('question');

        $parameters = [];

        if ($mode == self::MODE_PREVIEW) {
            $parameters = [
                'limit' => null,
                'random_rows' => null,
            ];

            foreach ($parameters as $questionCode => &$item) {
                $item = $this->ask($input, $output, $dialog, $questionCode);
            }
        }

        $parameters = array_merge(['mode' => $mode], $parameters);
        $isConfirmed = $this->confirmParameters($input, $output, $dialog, $parameters);

        if (!$isConfirmed) {
            $output->writeln('Exit.');
            return false;
        }

        /** @var \MageSuite\AddressSplitter\Model\Command\ExtractHouseNumber $extractHouseNumber */
        $extractHouseNumber = $this->extractHouserNumberFactory->create();
        $result = $extractHouseNumber->{$mode}($parameters);

        foreach ($result as $message) {
            $output->writeln($message);
        }

        return true;
    }

    protected function ask($input, $output, $dialog, $questionCode)
    {
        $questionData = $this->questions[$questionCode];

        $question = new \Symfony\Component\Console\Question\Question(
            sprintf(
                '<question>%s [default: %s]</question>',
                $questionData['question'],
                $questionData['default']
            ),
            $questionData['default']
        );

        return $dialog->ask($input, $output, $question);
    }

    protected function confirmParameters($input, $output, $dialog, $parameters)
    {
        foreach ($parameters as $questionCode => $value) {
            $message = sprintf('%s.......... %s', $questionCode, $value);
            $output->writeln($message);
        }

        if (strtolower($this->ask($input, $output, $dialog, 'confirm')) != 'y') {
            return false;
        }

        if ($parameters['mode'] == self::MODE_EXECUTE) {
            if (strtolower($this->ask($input, $output, $dialog, 'confirm_execute')) != 'y') {
                return false;
            }
        }

        return true;
    }
}
