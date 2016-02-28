<?php

namespace phantomd\ShopCart\modules\api\v1\models;

/**
 * Class ProductModel
 * 
 * @property integer $id ID product
 * @property string $name Product name
 * @property string $description Product description
 * @property integer $price Price
 */
class ProductRepository extends \phantomd\ShopCart\modules\base\BaseObject
{

    /**
     * @var array Repository
     */
    private static $_data = [
        [
            'id'          => 1,
            'name'        => 'Product #1',
            'description' => 'Product Description',
            'price'       => 50,
        ],
        [
            'id'          => 2,
            'name'        => 'Product #2',
            'description' => 'Product Description',
            'price'       => 100,
        ],
        [
            'id'          => 3,
            'name'        => 'Product #3',
            'description' => 'Product Description',
            'price'       => 150,
        ],
    ];

    /**
     * @inheritdoc
     */
    public static function getData()
    {
        return static::$_data;
    }

}
