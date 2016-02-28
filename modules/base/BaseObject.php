<?php

namespace phantomd\ShopCart\modules\base;

class BaseObject
{

    public function __construct($params = [])
    {
        if (false === empty($params) && is_array($params)) {
            foreach ($params as $key => $value) {
                $this->{$key} = $value;
            }
        }
        $this->init();
    }

    public function init()
    {
        
    }

    public static function shortClass()
    {
        return basename(get_called_class());
    }

}
