<?php

namespace MageSuite\AddressSplitter\Service\Address;

class HouseNumberSplitter
{
    /**
     * @var \VIISON\AddressSplitter\AddressSplitter
     */
    protected $addressSplitter;

    public function __construct(\VIISON\AddressSplitter\AddressSplitter $addressSplitter)
    {
        $this->addressSplitter = $addressSplitter;
    }

    public function splitStreet($street, $returnRaw = false)
    {
        $street = $this->removeDuplicates($street);
        $street = $this->mergeInOneLine($street);

        $splittedStreet = $this->splitStreetString($street);

        if ($returnRaw || is_string($splittedStreet)) {
            return $splittedStreet;
        }

        if (!empty($splittedStreet['additionToAddress1'])) {
            $splittedStreet['streetName'] = sprintf(
                '%s %s',
                $splittedStreet['additionToAddress1'],
                $splittedStreet['streetName']
            );
        }

        if (!empty($splittedStreet['additionToAddress2'])) {
            $splittedStreet['houseNumber'] = sprintf(
                '%s %s',
                $splittedStreet['houseNumber'],
                $splittedStreet['additionToAddress2']
            );
        }

        return [
            $splittedStreet['streetName'],
            $splittedStreet['houseNumber']
        ];
    }

    private function removeDuplicates($street)
    {
        if (substr_count($street, "\n") != 1) {
            return $street;
        }

        $streetParts = explode("\n", $street);

        if (count($streetParts) < 2) {
            return $street;
        }

        if (trim($streetParts[0]) != trim($streetParts[1])) {
            return $street;
        }

        return $streetParts[0];
    }

    private function mergeInOneLine($street)
    {
        return  preg_replace('/\s+/', ' ', $street);
    }

    protected function splitStreetString($street)
    {
        try {
            return $this->addressSplitter->splitAddress($street);
          } catch (\Exception $e) {
            return $street;
        }
    }
}
