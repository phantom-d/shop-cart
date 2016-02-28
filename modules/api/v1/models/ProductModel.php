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
class ProductModel extends \phantomd\ShopCart\modules\base\BaseModel
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'price'], 'required'],
            [['id', 'price'], 'integer'],
            [['name', 'description'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return ProductRepository::getData();
    }

}
