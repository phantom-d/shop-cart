<?php

namespace phantomd\ShopCart\modules\base\validators;

class Validator extends \phantomd\ShopCart\modules\base\BaseObject
{

    /**
     * @var mixed Value to validate
     */
    public $value = null;

    /**
     * @var string Error message
     */
    public $message = 'Error: %s';

    /**
     * Validate data value
     */
    public function validate()
    {
        return true;
    }

}
