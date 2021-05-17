<?php

namespace MageSuite\AddressSplitter\Test\Unit\Service\Address;

class HouseNumberSplitterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\AddressSplitter\Service\Address\HouseNumberSplitter
     */
    protected $houseNumberSplitter;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->houseNumberSplitter = $objectManager->get(\MageSuite\AddressSplitter\Service\Address\HouseNumberSplitter::class);
    }

    /**
     *
     * @param string $street
     * @param string $expectedStreet
     * @param string $expectedHouseNumber
     * @dataProvider dataProviderStreets
     */
    public function testItReturnsCorrectStreetParts($street, $expectedStreet, $expectedHouseNumber)
    {
        $result = $this->houseNumberSplitter->splitStreet($street);

        $this->assertEquals($expectedStreet, $result[0]);
        $this->assertEquals($expectedHouseNumber, $result[1]);
    }

    public function dataProviderStreets()
    {
        return [
            ['Strasseundhausnummer Str. 666g', 'Strasseundhausnummer Str.', '666g'],
            ['Geißlerweg 12a', 'Geißlerweg', '12a'],
            ['Geißlerweg 12 - a2', 'Geißlerweg', '12 - a2'],
            ['Erika-Mann-Str.53', 'Erika-Mann-Str.', '53'],
            ['Landsberger Straße 145 / 2', 'Landsberger Straße', '145 / 2'],
            ['Blodigstraße 7', 'Blodigstraße', '7'],
            ['Münchner Allee 145c', 'Münchner Allee', '145c'],
            ['Bergisch Gladbacher Straße 1248', 'Bergisch Gladbacher Straße', '1248'],
            ['Venloer Straße 1451', 'Venloer Straße', '1451'],
            ['Olpener Straße 1096', 'Olpener Straße', '1096'],
            ['Holzweg 10a/2', 'Holzweg', '10a/2'],
            ['Ganghoferstr. 68 a', 'Ganghoferstr.', '68 a'],
            ['Wiesentcenter, Bayreuther Str. 108, 2. Stock', 'Wiesentcenter Bayreuther Str.', '108 2. Stock'],
            ['1101 Madison St # 600', 'Madison St', '1101 # 600'],
            ['574 E 10th Street', 'E 10th Street', '574'],
            ['D 6, 2', 'D 6', '2'],
            ["12 Main Street \n 12 Main Street", 'Main Street', '12'],
            ["Olpener \n Straße 1096", 'Olpener Straße', '1096']
        ];
    }
}
