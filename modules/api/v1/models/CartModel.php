<?php

namespace phantomd\ShopCart\modules\api\v1\models;

class CartModel extends \phantomd\ShopCart\modules\base\BaseObject
{

    private $_products = null;

    public function init()
    {
        parent::init();
        if (false === empty([static::shortClass()])) {
            
        }
    }

}
