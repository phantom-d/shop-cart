<?php

namespace phantomd\ShopCart\modules\base\validators;

class StringValidator extends Validator
{

    /**
     * @inheritdoc
     */
    public $message = '%s must be a string.';

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return ('string' === gettype($this->value));
    }

}
