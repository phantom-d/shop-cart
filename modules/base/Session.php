<?php

namespace phantomd\ShopCart\modules\base;

class Session extends BaseObject
{

    private static $_data = null;

    public function init()
    {
        session_start();
        static::$_data = $_SESSION;
    }

    public function __get($name)
    {
        $return = null;

        if ($name) {
            if (isset(static::$_data[$name])) {
                $return = static::$_data[$name];
            }
        }

        return $return;
    }

    public function __set($name, $value)
    {
        if (mb_strlen($name) > 0) {
            static::$_data[$name] = $value;
        }
    }

    public function send()
    {
        $_SESSION = static::$_data;
    }

    public function __destruct()
    {
        $this->send();
    }

}
