<?php

namespace App\Repository;

use App\MySqlAdapter;

abstract class Orders
{
    /**
     * @param array $products
     *
     * @return string
     */
    public static function createNewOrder(array $products): string
    {
        $adapter = MySqlAdapter::getInstance();
        $number = uniqid();

        $adapter->beginTransaction();
        try {
            $adapter->prepareQuery(/** @lang MySQL */ "
                INSERT INTO `app`.`orders` (`number`, `status`)
                VALUE (:number, 'new');
            ");
            $adapter->executeQuery([
                'number' => $number,
            ]);
            $orderId = $adapter->getLastInsertId();

            $adapter->prepareQuery(/** @lang MySQL */ "
                INSERT INTO `app`.`bindings` (`product_id`, `order_id`, `count`)
                VALUE (:product_id, :order_id, :count);
            ");
            foreach ($products as $product) {
                $adapter->executeQuery([
                    'product_id' => $product,
                    'order_id' => $orderId,
                    'count' => rand(1, 99),
                ]);
            }

            $adapter->commitTransaction();
        } catch (\PDOException $exception) {
            $adapter->rollBackTransaction();
            throw $exception;
        }

        return $number;
    }

    /**
     * @param string $number
     *
     * @return bool
     */
    public static function findOrderWithStatusNew(string $number): bool
    {
        $adapter = MySqlAdapter::getInstance();

        $adapter->prepareQuery(/** @lang MySQL */ "
            SELECT `id`
            FROM `app`.`orders`
            WHERE `number` = :number
              AND `status` = 'new'
            LIMIT 1;
        ");
        $adapter->executeQuery([
            'number' => $number,
        ]);
        $data = $adapter->getDataAsArray();
        $id = reset($data)['id'] ?? null;

        if (null === $id) {
            return false;
        }

        return true;
    }

    /**
     * @param string $number
     *
     * @return int
     */
    public static function getOrderAmount(string $number): int
    {
        $adapter = MySqlAdapter::getInstance();

        $adapter->prepareQuery(/** @lang MySQL */ "
            SELECT `b`.`count`, `p`.`price`
            FROM `app`.`orders` AS `o`
            LEFT JOIN `app`.`bindings` AS `b` ON `o`.`id` = `b`.`order_id`
            LEFT JOIN `app`.`products` AS `p` ON `b`.`product_id` = `p`.`id`
            WHERE `o`.`number` = :number;
        ");
        $adapter->executeQuery([
            'number' => $number,
        ]);
        $data = $adapter->getDataAsArray();

        $amount = 0;
        foreach ($data as $datum) {
            $amount += $datum['count'] * $datum['price'];
        }

        return $amount;
    }

    /**
     * @param string $number
     */
    public static function payOrder(string $number)
    {
        $adapter = MySqlAdapter::getInstance();

        $adapter->prepareQuery(/** @lang MySQL */ "
            UPDATE `app`.`orders`
            SET `status` = 'payed'
            WHERE `number` = :number;
        ");
        $adapter->executeQuery([
            'number' => $number,
        ]);
    }
}
