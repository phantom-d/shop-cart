<?php

namespace phantomd\ShopCart\modules\base;

class BaseController extends BaseObject
{

    /**
     * @var array
     */
    public $route = null;

    /**
     * @var array
     */
    public $path = null;

    /**
     * @var array
     */
    protected $get = null;

    /**
     * @var array
     */
    protected $post = null;

    /**
     * @var string
     */
    protected $requestPath = '';

    /**
     * @var string
     */
    protected $requestMethod = '';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->get           = $_GET;
        $this->post          = $_POST;
        $this->requestPath   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->path          = array_filter(explode('/', $this->requestPath));
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Execute requested action
     */
    public function execute()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $verbs  = $this->verbs();
        if (isset($verbs[$this->requestMethod])) {
            $action           = 'action' . ucfirst(mb_strtolower($verbs[$method]));
            $reflectionMethod = new \ReflectionMethod($this, $action);

            $parsePath = array_slice($this->path, count($this->route));

            $args   = [];
            $errors = [];

            if ($params = $reflectionMethod->getParameters()) {
                foreach ($params as $key => $param) {
                    if (isset($parsePath[$key])) {
                        $args[$param->name] = $parsePath[$key];
                    } else {
                        $errors[] = [
                            'code'    => 'required',
                            'message' => ucfirst(mb_strtolower(explode('_', $param->name)[0])) . ' cannot be blank.',
                            'name'    => $param->name,
                        ];
                    }
                }
                if ($errors) {
                    throw new \phantomd\ShopCart\modules\base\HttpException(400, 'Invalid data parameters', 0, null, $errors);
                }
            }
            if (count($parsePath) === count($params)) {
                return call_user_func_array([$this, $action], $args);
            }
        }
        throw new HttpException(404, 'Unable to resolve the request "' . $this->requestPath . '".', 0, null, $errors);
    }

    /**
     * Render content 
     * 
     * @param array $data
     * @return string
     */
    public function render($data = [])
    {
        return json_encode(['data' => $data], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get options for current controller
     */
    public function actionOptions()
    {
        return $this->render($this->verbs());
    }

    /**
     * Declares the allowed HTTP verbs.
     */
    protected function verbs()
    {
        return [
            'OPTIONS' => 'options',
        ];
    }

}
