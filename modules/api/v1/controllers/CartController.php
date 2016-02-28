<?php

namespace phantomd\ShopCart\modules\api\v1\controllers;

class CartController extends \phantomd\ShopCart\modules\base\BaseController
{

    public function actionIndex()
    {
        $data = [];

        $this->render($data);
    }

    public function actionAdd()
    {
        $data = [];

        $this->render($data);
    }

    public function actionDelete($id)
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
            'POST'    => 'add',
            'DELETE'  => 'delete',
            'OPTIONS' => 'options',
        ];
    }

}
