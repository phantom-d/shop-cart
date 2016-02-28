<?php

namespace phantomd\ShopCart\modules\base;

class Cookies extends BaseObject
{

    private static $_data = null;

    private $host         = null;

    public function init()
    {
        session_start();
        static::$_data = $_COOKIE;
        $this->host = preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']);
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

    public function __set($name, $value, $expired = 0)
    {
        if (mb_strlen($name) > 0) {
            static::$_data[$name] = $value;
            setcookie($name, $value, (int)$expired, '/', $this->host);
        }
    }

    public function send()
    {
        $_COOKIE = static::$_data;
    }

    public function __destruct()
    {
        $this->send();
    }

}
