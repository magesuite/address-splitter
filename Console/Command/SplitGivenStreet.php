<?php

namespace MageSuite\AddressSplitter\Console\Command;

class SplitGivenStreet extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \MageSuite\AddressSplitter\Service\Address\HouseNumberSplitterFactory
     */
    protected $houseNumberSplitterFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    public function __construct(
        \MageSuite\AddressSplitter\Service\Address\HouseNumberSplitterFactory $houseNumberSplitterFactory,
        \Magento\Framework\App\State $state
    ) {
        parent::__construct();

        $this->houseNumberSplitterFactory = $houseNumberSplitterFactory;
        $this->state = $state;
    }

    protected function configure()
    {
        $this->addArgument(
            'street',
            \Symfony\Component\Console\Input\InputArgument::REQUIRED,
            'Street (string) for splitter',
        );

        $this->addOption(
            'raw_result',
            '-r',
            \Symfony\Component\Console\Input\InputArgument::OPTIONAL,
            'Return raw result'
        );

        $this->setName('address:splitter:split')
            ->setDescription('Command to test: split given street into two parts, street and house number');
    }

    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);

        $street = $input->getArgument('street');
        $rawResult = $input->getOption('raw_result') ?? false;

        /** @var \MageSuite\AddressSplitter\Service\Address\HouseNumberSplitter $houseNumberSplitter */
        $houseNumberSplitter = $this->houseNumberSplitterFactory->create();
        $result = $houseNumberSplitter->splitStreet($street, $rawResult);

        $output->writeln(print_r($result, true));
        return 0;
    }
}
