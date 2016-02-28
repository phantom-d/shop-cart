<?php

namespace phantomd\ShopCart\modules\base;

class HttpException extends \Exception
{

    public $statusCode = 200;

    /**
     * Constructor.
     * @param integer $status HTTP status code, such as 404, 500, etc.
     * @param string $message error message
     * @param integer $code error code
     * @param \Exception $previous The previous exception used for the exception chaining.
     */
    public function __construct($status, $message = '', $code = 0, \Exception $previous = null)
    {
        $this->statusCode = $status;

        if (empty($message)) {
            $message = $this->getName();
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        $return = 'Error';

        if (isset(Application::$httpStatuses[$this->statusCode])) {
            $return = Application::$httpStatuses[$this->statusCode];
        }

        return $return;
    }

}
