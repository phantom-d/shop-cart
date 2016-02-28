<?php

namespace phantomd\ShopCart\modules\api\v1\controllers;

class ProductsController extends \phantomd\ShopCart\modules\base\BaseController
{

    public function actionIndex()
    {
        $data = [];

        $this->render($data);
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
