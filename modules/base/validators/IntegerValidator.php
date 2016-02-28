<?php

namespace phantomd\ShopCart\modules\base\validators;

class IntegerValidator extends Validator
{

    /**
     * @inheritdoc
     */
    public $message = '%s must be integer.';

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return ('integer' === gettype($this->value));
    }

}
