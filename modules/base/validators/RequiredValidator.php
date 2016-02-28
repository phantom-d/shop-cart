<?php

namespace phantomd\ShopCart\modules\base\validators;

class RequiredValidator extends Validator
{

    /**
     * @inheritdoc
     */
    public $message = '%s cannot be blank.';

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return (false === empty($this->value));
    }

}
