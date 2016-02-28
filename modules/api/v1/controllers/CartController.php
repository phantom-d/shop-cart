<?php

namespace phantomd\ShopCart\modules\api\v1\controllers;

use phantomd\ShopCart\modules\base\Application;
use phantomd\ShopCart\modules\api\v1\models\ProductModel;
use phantomd\ShopCart\modules\api\v1\models\CartModel;

class CartController extends \phantomd\ShopCart\modules\base\BaseController
{

    public function actionIndex()
    {
        $data = CartModel::model();

        return $this->render($data);
    }

    public function actionAdd()
    {
        $post = $_POST;
        $cart = CartModel::model();

        $id       = isset($post['product_id']) ? $post['product_id'] : null;
        $quantity = isset($post['quantity']) ? $post['quantity'] : null;

        if ($errors = $cart->addProduct($id, $quantity)) {
            throw new \phantomd\ShopCart\modules\base\HttpException(400, 'Invalid data parameters', 0, null, $errors);
        }
        $cart->save();
    }

    public function actionDelete($id)
    {
        $id   = (int)$id;
        $cart = CartModel::model();

        if ($errors = $cart->deleteProduct($id)) {
            throw new \phantomd\ShopCart\modules\base\HttpException(400, 'Invalid data parameters', 0, null, $errors);
        }
        $cart->save();
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'GET'     => 'index',
            'POST'    => 'add',
            'DELETE'  => 'delete',
            'OPTIONS' => 'options',
        ];
    }

}
