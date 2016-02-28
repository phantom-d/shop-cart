<?php

namespace phantomd\ShopCart\modules\base;

/**
 * ErrorHandler handles uncaught PHP errors and exceptions.
 */
class ErrorHandler extends BaseObject
{

    /**
     * @var integer the size of the reserved memory. A portion of memory is pre-allocated so that
     * when an out-of-memory issue occurs, the error handler is able to handle the error with
     * the help of this reserved memory. If you set this value to be 0, no memory will be reserved.
     * Defaults to 256KB.
     */
    public $memoryReserveSize = 262144;

    /**
     * @var string Used to reserve memory for fatal error handler.
     */
    private $_memoryReserve;

    /**
     * Register this error handler
     */
    public function register()
    {
        ini_set('display_errors', false);
        set_exception_handler([$this, 'handleException']);
        set_error_handler([$this, 'handleError']);
        if ($this->memoryReserveSize > 0) {
            $this->_memoryReserve = str_repeat('x', $this->memoryReserveSize);
        }
        register_shutdown_function([$this, 'handleFatalError']);
    }

    /**
     * Unregisters this error handler by restoring the PHP error and exception handlers.
     */
    public function unregister()
    {
        restore_error_handler();
        restore_exception_handler();
    }

    /**
     * Handles uncaught PHP exceptions.
     *
     * This method is implemented as a PHP exception handler.
     *
     * @param \Exception $exception the exception that is not caught
     */
    public function handleException($exception)
    {
        // disable error capturing to avoid recursive errors while handling exceptions
        $this->unregister();

        // set preventive HTTP status code to 500 in case error handling somehow fails and headers are sent
        // HTTP exceptions will override this value in renderException()
        if (PHP_SAPI !== 'cli') {
            http_response_code(500);
        }

        try {
            $this->renderException($exception);
        } catch (\Exception $e) {
            // an other exception could be thrown while displaying the exception
            $msg = "An Error occurred while handling another error:\n";
            $msg .= (string)$e;
            $msg .= "\nPrevious exception:\n";
            $msg .= (string)$exception;
            echo 'An internal server error occurred.';
            $msg .= "\n\$_SERVER = " . VarDumper::export($_SERVER);
            error_log($msg);
            exit(1);
        }
    }

    /**
     * Handles PHP execution errors such as warnings and notices.
     *
     * This method is used as a PHP error handler. It will simply raise an [[ErrorException]].
     *
     * @param integer $code the level of the error raised.
     * @param string $message the error message.
     * @param string $file the filename that the error was raised in.
     * @param integer $line the line number the error was raised at.
     * @return boolean whether the normal error handler continues.
     *
     * @throws ErrorException
     */
    public function handleError($code, $message, $file, $line)
    {
        if (error_reporting() & $code) {
            $exception = new ErrorException($message, $code, $code, $file, $line);

            // in case error appeared in __toString method we can't throw any exception
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            array_shift($trace);
            foreach ($trace as $frame) {
                if ($frame['function'] === '__toString') {
                    $this->handleException($exception);
                    if (defined('HHVM_VERSION')) {
                        flush();
                    }
                    exit(1);
                }
            }

            throw $exception;
        }
        return false;
    }

    /**
     * Handles fatal PHP errors
     */
    public function handleFatalError()
    {
        unset($this->_memoryReserve);
        $error = error_get_last();

        if (ErrorException::isFatalError($error)) {
            $exception = new ErrorException($error['message'], $error['type'], $error['type'], $error['file'], $error['line']);
            $this->renderException($exception);
            exit(1);
        }
    }

    /**
     * Renders the exception.
     * @param \Exception $exception the exception to be rendered.
     */
    protected function renderException($exception)
    {
        $app = Application::$app;

        $app->setContent(json_encode($this->convertExceptionToArray($exception), JSON_UNESCAPED_UNICODE));

        if ($exception instanceof HttpException) {
            $app::setStatusCode($exception->statusCode);
        } else {
            $app::setStatusCode(500);
        }

        $app->send();
    }

    /**
     * Converts an exception into an array.
     * @param \Exception $exception the exception being converted
     * @return array the array representation of the exception.
     */
    protected function convertExceptionToArray($exception)
    {
        $array = [
            'error' => []
        ];

        if ($exception instanceof HttpException) {
            if ($exception->params) {
                $array['error']['params'] = $exception->params;
            }
            if (isset(Application::$httpTypes[$exception->statusCode])) {
                $array['error']['type'] = Application::$httpTypes[$exception->statusCode];
            }
        }

        $array['error']['message'] = $exception->getMessage();

        if (empty($array['error']['type'])) {
            if ($exception instanceof HttpException) {
                $array['debug']['status'] = $exception->statusCode;
            }

            $name = 'Exception';
            if ($exception instanceof Exception || $exception instanceof ErrorException) {
                $name = $exception->getName();
            }

            $array['debug']['name']        = $name;
            $array['debug']['code']        = $exception->getCode();
            $array['debug']['type']        = get_class($exception);
            $array['debug']['file']        = $exception->getFile();
            $array['debug']['line']        = $exception->getLine();
            $array['debug']['stack-trace'] = explode("\n", $exception->getTraceAsString());
        }

        if (($prev = $exception->getPrevious()) !== null) {
            $array['previous'] = $this->convertExceptionToArray($prev);
        }

        return $array;
    }

}
