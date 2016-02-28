<?php

namespace phantomd\ShopCart\modules\api\v1\controllers;

use phantomd\ShopCart\modules\api\v1\models\ProductModel;

class ProductsController extends \phantomd\ShopCart\modules\base\BaseController
{

    /**
     * Get all products
     *
     * @return string
     */
    public function actionIndex()
    {
        $data = ProductModel::findAll();
        return $this->render($data);
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'GET'     => 'index',
            'OPTIONS' => 'options',
        ];
    }

}
