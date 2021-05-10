<?php

namespace MageSuite\AddressSplitter\Test\Integration\Model\Command;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class ExtractHouseNumberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerModelFactory;

    /**
     * @var \MageSuite\AddressSplitter\Model\Command\ExtractHouseNumber
     */
    protected $extractHouseNumber;

    public function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->customerModelFactory = $objectManager->get(\Magento\Customer\Model\CustomerFactory::class);
        $this->extractHouseNumber = $objectManager->get(\MageSuite\AddressSplitter\Model\Command\ExtractHouseNumber::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Customer/_files/customer_with_addresses.php
     */
    public function testItUpdatesStreetInCustomerAddress()
    {
        $websiteId = 1;
        $email = 'customer_with_addresses@test.com';

        $customer = $this->customerModelFactory->create();
        $customer->setWebsiteId($websiteId)->loadByEmail($email);

        $addresses = $customer->getAddresses();
        $address = array_shift($addresses);
        $this->assertEquals([0 => 'CustomerAddress1'], $address->getStreet());

        $this->extractHouseNumber->execute([]);

        $customer = $this->customerModelFactory->create();
        $customer->setWebsiteId($websiteId)->loadByEmail($email);

        $addresses = $customer->getAddresses();
        $address = array_shift($addresses);
        $this->assertEquals([0 => 'CustomerAddress', 1 => 1], $address->getStreet());
    }
}
