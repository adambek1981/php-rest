<?php

namespace App\Repository;

use App\Entity\Product;
use App\MySqlAdapter;

abstract class Products
{
    /**
     * @param array|Product[] $products
     */
    public static function store(array $products)
    {
        $adapter = MySqlAdapter::getInstance();

        $adapter->beginTransaction();
        try {
            $adapter->prepareQuery(/** @lang MySQL */ "
                INSERT INTO `app`.`products` (`name`, `price`)
                VALUE (:name, :price);
            ");

            foreach ($products as $product) {
                $adapter->executeQuery([
                    'name' => $product->name,
                    'price' => $product->price,
                ]);
            }

            $adapter->commitTransaction();
        } catch (\PDOException $exception) {
            $adapter->rollBackTransaction();
            throw $exception;
        }
    }

    /**
     * @param array $productIdList
     *
     * @return array
     */
    public static function getNotExisted(array $productIdList): array
    {
        $adapter = MySqlAdapter::getInstance();

        $adapter->prepareQuery(/** @lang MySQL */ "
            SELECT `id`
            FROM `app`.`products`
            ORDER BY `id`
            LIMIT 1000; 
        ");
        $adapter->executeQuery();
        $ids = array_column($adapter->getDataAsArray(), 'id');

        $notExisted = [];
        foreach ($productIdList as $productId) {
            if (!in_array($productId, $ids)) {
                $notExisted[] = $productId;
            }
        }

        return $notExisted;
    }
}
