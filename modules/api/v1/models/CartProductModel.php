<?php

namespace phantomd\ShopCart\modules\api\v1\models;

/**
 * Class CartProductModel
 * 
 * @property integer $product_id ID product
 * @property integer $quantity Quantity
 * @property integer $sum Summa
 */
class CartProductModel extends \phantomd\ShopCart\modules\base\BaseModel
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($product = $this->product) {
            $this->sum = $product->price * $this->quantity;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'quantity',], 'required'],
            [['product_id', 'quantity', 'sum'], 'integer'],
            [['quantity', 'sum'], 'default', 0],
        ];
    }

    /**
     * Get current product model
     *
     * @return \phantomd\ShopCart\modules\api\v1\models\ProductModel
     */
    public function getProduct()
    {
        return ProductModel::findOne($this->product_id);
    }

}
