<?php

namespace phantomd\ShopCart\modules\base;

class BaseObject
{

    /**
     * Initialization object
     *
     * @param array $params
     */
    public function __construct($params = [])
    {
        if (false === empty($params) && is_array($params)) {
            foreach ($params as $key => $value) {
                $this->{$key} = $value;
            }
        }
        $this->init();
    }

    /**
     * Event after initialization object
     */
    public function init()
    {

    }

    /**
     * Get short class name
     *
     * @return string
     */
    public static function shortClass()
    {
        return array_pop(explode('\\', get_called_class()));
    }

    /**
     * Magick get properties
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst(mb_strtolower($name));
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method]);
        }
    }

}
