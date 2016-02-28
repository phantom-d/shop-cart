<?php

namespace phantomd\ShopCart\modules\api\v1\models;

use phantomd\ShopCart\modules\base\Application;

/**
 * Class CartModel
 */
class CartModel extends \phantomd\ShopCart\modules\base\BaseModel
{

    /**
     * @var integer Total sum
     */
    public $total_sum = 0;

    /**
     * @var integer Products count
     */
    public $products_count = 0;

    /**
     * @var \phantomd\ShopCart\modules\api\v1\model\ProductModel[]
     */
    public $products = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $app      = Application::$app;
        $cartName = static::shortClass();

        if ($cart = $app::$cookies->{$cartName}) {
            $cart = json_decode($cart, true);
            foreach ($this->asArray() as $name => $value) {
                $this->{$name} = $cart[$name];
            }
            if (isset($cart['products'])) {
                foreach ($cart['products'] as $product) {
                    $this->products[] = CartProductModel::model($product);
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['total_sum', 'products_count'], 'integer'],
            [['total_sum', 'products_count'], 'default', 0],
        ];
    }

    public function save()
    {
        $return = false;
        if ($this->validate()) {
            $app      = Application::$app;
            $data     = $this->asArray();
            $cartName = static::shortClass();

            foreach ($this->products as $product) {
                if ($product->validate()) {
                    $data['products'][] = $product->asArray();
                }
            }
            $app::$cookies->{$cartName} = json_encode($data, JSON_UNESCAPED_UNICODE);

            $return = true;
        }
        return $return;
    }

    public function countCart()
    {
        $return = [
            'products_count' => 0,
            'total_sum'      => 0,
        ];
        if ($this->products) {
            foreach ($this->products as $key => $product) {
                if ($product->validate() && $product->quantity > 0) {
                    if ($row = $product->product) {
                        $return['products_count'] += $product->quantity;
                        $return['total_sum'] += $row->price * $product->quantity;
                    }
                } else {
                    unset($this->products[$key]);
                }
            }
            foreach ($return as $name => $value) {
                if ($value < 0) {
                    $value = 0;
                }
                $this->{$name} = $value;
            }
        }
        return $return;
    }

    /**
     * Add product to cart
     *
     * @param integer $id ID product
     * @param integer $quantity Quantity
     * @return mixed Errors validating product data
     */
    public function addProduct($id, $quantity = 1)
    {
        $return = null;
        $params = [
            'product_id' => (int)$id,
            'quantity'   => (int)$quantity,
        ];

        $new = true;
        if ($this->products) {
            foreach ($this->products as $key => $product) {
                if ($product->product_id === $params['product_id']) {
                    $this->products[$key]->quantity += $params['quantity'];
                    $new = false;
                }
            }
        }

        if ($new) {
            $model = CartProductModel::model($params);
            if ($model->validate()) {
                $this->products[] = $model;
            } else {
                $return = $model->getErrors();
            }
        }

        if (empty($return)) {
            $countCart            = $this->countCart();
            $this->products_count = $countCart['products_count'];
            $this->total_sum      = $countCart['total_sum'];
        }

        return $return;
    }

    /**
     * Delete product from cart
     *
     * @param integer $id ID product
     * @return mixed Errors validating product data
     */
    public function deleteProduct($id)
    {
        $return = null;
        $check  = true;
        if ($this->products) {
            foreach ($this->products as $key => $product) {
                if ($product->product_id === $id) {
                    --$this->products[$key]->quantity;
                    $check = false;
                }
            }
        }

        if ($check) {
            $this->setError('product_id', "Product '{$id}' is not found.", 'required');
        }

        $return = $this->getErrors();

        if (empty($return)) {
            $countCart            = $this->countCart();
            $this->products_count = $countCart['products_count'];
            $this->total_sum      = $countCart['total_sum'];
        }

        return $return;
    }

}
