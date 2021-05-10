<?php

namespace MageSuite\AddressSplitter\Model\ResourceModel\Customer;

class Address
{
    const UPDATE_BATCH_SIZE = 200;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->connection = $resourceConnection->getConnection();
    }

    public function getStreetValues($parameters)
    {
        $select = $this->connection
            ->select()
            ->from(['main_table' => $this->connection->getTableName('customer_address_entity')], ['entity_id', 'parent_id', 'street']);

        $randomRows = $parameters['random_rows'] ?? false;

        if ($randomRows) {
            $select->order(new \Zend_Db_Expr('RAND()'));
        }

        $count = $parameters['limit'] ?? null;

        if ($count) {
            $select->limit($count);
        }

        return $this->connection->fetchAll($select);
    }

    public function updateStreetValues($data)
    {
        $tableName = $this->connection->getTableName('customer_address_entity');
        $batches = array_chunk($data, self::UPDATE_BATCH_SIZE);

        foreach ($batches as $batch) {
            $this->connection->insertOnDuplicate($tableName, $batch);
        }
    }
}
