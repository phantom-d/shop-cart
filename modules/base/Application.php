<?php

namespace phantomd\ShopCart\modules\base;

class Application extends BaseObject
{

    /**
     * @var string Final content data
     */
    private $_content = '';

    /**
     * @var \phantomd\ShopCart\modules\base\Application Application object
     */
    public static $app = null;

    /**
     * @var \phantomd\ShopCart\modules\base\Session Session data
     */
    public static $session = null;

    /**
     * @var \phantomd\ShopCart\modules\base\Cookies Cookies data
     */
    public static $cookies = null;

    /**
     * @var array HTTP status codes 
     */
    public static $httpStatuses = [
        200 => 'OK',
        400 => 'Bad Request',
        404 => 'Not Found',
        500 => 'Internal Server Error',
    ];

    /**
     * @var array HTTP status codes 
     */
    public static $httpTypes = [
        400 => 'invalid_param_error',
        404 => 'invalid_request_error',
    ];

    /**
     * @var array Router config
     */
    public $router = null;

    /**
     * @var \phantomd\ShopCart\modules\base\BaseController
     */
    public $controller = null;

    /**
     * @var integer the HTTP status code to send with the response.
     */
    private static $_statusCode = 200;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        static::$app = &$this;
    }

    /**
     * Run application
     */
    public function run()
    {
        static::$session = new Session;
        static::$cookies = new Cookies;
        (new ErrorHandler)->register();

        $this->router();
        $this->send();
    }

    /**
     * Router application
     */
    public function router()
    {
        $parseUrl = parse_url($_SERVER['REQUEST_URI']);
        $params   = '';
        foreach ($this->router as $route => $class) {
            $parseUri = array_filter(
                explode('/', $parseUrl['path'])
            );

            $route = array_filter(
                explode('/', $route)
            );

            $parseRoute = array_slice($parseUri, 0, count($route));

            if (implode('/', $parseRoute) === implode('/', $route)) {
                $this->controller = new $class(['route' => $route]);
                break;
            }
        }

        if (is_object($this->controller)) {
            $this->setContent($this->controller->execute());
        } else {
            throw new HttpException(404, 'Unable to resolve the request "' . $parseUrl['path'] . '".');
        }
    }

    /**
     * Send content to client
     */
    public function send()
    {
        $this->setHeaderStatus();
        echo $this->getContent();
    }

    /**
     * Set HTTP status header
     */
    public function setHeaderStatus()
    {
        if (false === headers_sent()) {
            $statusCode = static::getStatusCode();
            if (false === isset(static::$httpStatuses[$statusCode])) {
                $this->setStatusCode(200);
            }
            header("HTTP/1.1 {$statusCode} " . static::$httpStatuses[$statusCode]);
            header('Content-Type: application/json');
        }
    }

    /**
     * Set HTTP status code
     * 
     * @param integer $code
     */
    public static function setStatusCode($code)
    {
        static::$_statusCode = (int)$code;
    }

    /**
     * Get HTTP status code
     * 
     * @return integer
     */
    public static function getStatusCode()
    {
        return static::$_statusCode;
    }

    /**
     * Set content
     * 
     * @param string $data
     * @param bool $merge
     */
    public function setContent($data, $merge = false)
    {
        if ($merge) {
            $this->_content .= (string)$data;
        } else {
            $this->_content = (string)$data;
        }
    }

    /**
     * Get content
     * 
     * @return string
     */
    public function getContent()
    {
        return $this->_content;
    }

}
