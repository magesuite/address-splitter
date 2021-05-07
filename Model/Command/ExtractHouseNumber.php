<?php

namespace MageSuite\AddressSplitter\Model\Command;

class ExtractHouseNumber
{
    /**
     * @var \MageSuite\AddressSplitter\Model\ResourceModel\Customer\Address
     */
    protected $customerAddressResource;

    /**
     * @var \MageSuite\AddressSplitter\Service\Address\HouseNumberSplitter
     */
    protected $houseNumberSplitter;

    /**
     * @var \MageSuite\AddressSplitter\Model\Command\ExportResultToFile
     */
    protected $exportResultToFile;

    public function __construct(
        \MageSuite\AddressSplitter\Model\ResourceModel\Customer\Address $customerAddressResource,
        \MageSuite\AddressSplitter\Service\Address\HouseNumberSplitter $houseNumberSplitter,
        \MageSuite\AddressSplitter\Model\Command\ExportResultToFile $exportResultToFile
    ) {
        $this->customerAddressResource = $customerAddressResource;
        $this->houseNumberSplitter = $houseNumberSplitter;
        $this->exportResultToFile = $exportResultToFile;
    }

    public function execute($parameters, $isPreview = false)
    {
        $streets = $this->customerAddressResource->getStreetValues($parameters);

        if (empty($streets)) {
            return $this->returnResult(0, 0, null);
        }

        $parsedStreets = $this->parseStreets($streets);

        if (empty($parsedStreets)) {
            return $this->returnResult(count($streets), 0, null);
        }

        $resultFilePath = $this->exportResultToFile->execute($parsedStreets);

        if (!$isPreview) {
            $this->updateStreetValues($parsedStreets);
        }

        return $this->returnResult(count($streets), count($parsedStreets), $resultFilePath);
    }

    public function preview($parameters)
    {
        return $this->execute($parameters, true);
    }

    protected function parseStreets($streets)
    {
        $result = [];
        foreach ($streets as $street) {
            $parsedStreet = $this->houseNumberSplitter->splitStreet($street['street']);

            if (!is_array($parsedStreet)) {
                continue;
            }

            $parsedStreet = implode("\n", $parsedStreet);

            if ($street['street'] == $parsedStreet) {
                continue;
            }

            $street['old_value'] = $street['street'];
            $street['street'] = $parsedStreet;
            $result[] = $street;
        }

        return $result;
    }

    protected function updateStreetValues($parsedStreets)
    {
        foreach ($parsedStreets as &$street) {
            unset($street['old_value']);
        }

        $this->customerAddressResource->updateStreetValues($parsedStreets);
    }

    protected function returnResult($countStreet, $countParsedStreet, $resultFilePath)
    {
        return [
            'All results = ' . $countStreet,
            'Parsed results = ' . $countParsedStreet,
            'Path to file with parsed results = ' . $resultFilePath
        ];
    }
}
