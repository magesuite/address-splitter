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

    protected function splitStreetString($street)
    {
        try {
            return $this->addressSplitter->splitAddress($street);
          } catch (\Exception $e) {
            return $street;
        }
    }
}
