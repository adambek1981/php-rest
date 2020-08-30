<?php

namespace App\Entity;

class Product
{
    /**
     * @return Product
     */
    public static function generate(): Product
    {
        $product = new Product();

        $product->id = 0;
        $product->name = ucfirst(
            substr(
                str_shuffle('abcdefghijklmnopqrstuvwxyz'),
                0,
                rand(3, 10)
            )
        );
        $product->price = rand(100, 10000);

        return $product;
    }

    ####################################################################################################################

    /** @var int $id */
    public $id;

    /** @var string $name */
    public $name;

    /** @var int $price */
    public $price;
}
