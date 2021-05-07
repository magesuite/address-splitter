<?php

namespace MageSuite\AddressSplitter\Model\Command;

class ExportResultToFile
{
    const TEMP_DIR = 'parsed_street';
    const TEMP_FILE = 'parsed_street/result.csv';

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $directory;

    public function __construct(\Magento\Framework\Filesystem $filesystem
    ) {
        $this->directory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
    }

    public function execute($result)
    {
        $csvHeaders = array_keys($result[0]);

        $this->directory->create(self::TEMP_DIR);

        $stream = $this->directory->openFile(self::TEMP_FILE);
        $stream->lock();

        $stream->writeCsv($csvHeaders);

        foreach ($result as $row) {
            $stream->writeCsv($row);
        }

        return $this->directory->getAbsolutePath(self::TEMP_FILE);
    }
}
