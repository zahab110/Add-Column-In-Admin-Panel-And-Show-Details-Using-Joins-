<?php
namespace Custominput\Admin\Model\Plugin\Sales\Order;

class Grid
{
    public static $table = 'sales_order_grid';

    public function afterSearch($interceptor, $collection)
    {
        if ($collection->getMainTable() === $collection->getConnection()->getTableName(self::$table)) {
            $salesOrderAddressTable = $collection->getConnection()->getTableName('sales_order_address');

            $collection
                ->getSelect()
                ->joinLeft(
                    ['soa' => $salesOrderAddressTable],
                    "soa.parent_id = main_table.entity_id",
                    [
                        'passport_number' => 'soa.passport_number'
                    ]
                );

            // Map the field for filtering
            $collection->addFilterToMap('passport_number', 'soa.passport_number');
        }

        return $collection;
    }
}