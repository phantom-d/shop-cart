<?php

namespace phantomd\ShopCart\modules\base;

class BaseModel extends BaseObject
{

    /**
     * @var array Validation errors
     */
    protected $errors = [];

    /**
     * Declares the allowed HTTP verbs.
     */
    protected function rules()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->fields) {
            foreach ($this->fields as $key => $value) {
                if (isset($this->{$key})) {
                    $value = $this->{$key};
                }
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Validate data value
     */
    public function validate()
    {
        $return = true;
        if ($rules  = $this->rules()) {
            foreach ($rules as $rule) {
                $fields  = isset($rule[0]) ? $rule[0] : null;
                $type    = isset($rule[1]) ? mb_strtolower($rule[1]) : null;
                $message = isset($rule[2]) ? $rule[2] : null;
                if ($fields && $type) {
                    $class = 'phantomd\\ShopCart\\modules\\base\\validators\\' . ucfirst($type) . 'Validator';
                    if (is_array($fields) && class_exists($class)) {
                        foreach ($fields as $field) {
                            $config = ['value' => $this->{$field}];
                            if ($message) {
                                $config['message'] = $message;
                            }
                            $validator = new $class($config);
                            if (false === $validator->validate()) {
                                $return = false;
                                $this->setError($field, $validator->message, $type);
                            }
                        }
                    }
                }
            }
        }

        return $return;
    }

    public function setError($name, $message, $code = '')
    {
        $this->errors[$name][] = [
            'code'    => $code,
            'message' => sprintf($message, ucfirst(explode('_', $name)[0])),
            'name'    => $name,
        ];
    }

    public function getError($name)
    {
        return isset($this->errors[$name]) ? $this->errors[$name] : null;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Repository data
     *
     * @return array
     */
    public function getData()
    {
        return [];
    }

    /**
     * Get params for request
     *
     * @param mixed $params
     * @return array
     */
    public function createQuery($params)
    {
        $return = [];
        if (is_array($params)) {
            $fields = $this->fields;
            foreach ($params as $key => $value) {
                if (isset($fields[$key])) {
                    $return[$key] = $value;
                }
            }
        } else {
            if (false === empty($params)) {
                $return = [
                    'id' => $params,
                ];
            }
        }

        return $return;
    }

    /**
     * Find one row from repository
     *
     * @param mixed $params
     * @return array
     */
    public static function findOne($params = [])
    {
        $return = null;

        if ($data = static::findAll($params, 1)) {
            $return = $data[0];
        }
        return $return;
    }

    /**
     * Find any rows from repository
     *
     * @param mixed $params
     * @return array
     */
    public static function findAll($params = [], $limit = 0, $page = 0)
    {
        $return = [];
        $model  = static::model();
        $query  = $model->createQuery($params);

        if ($data = $model->getData()) {
            if (0 === (int)$limit) {
                $limit = count($data);
                $page  = 0;
            }
            $start = $limit * $page;
            $end   = count($data);

            for ($i = $start; $i < $end; $i++) {
                $add = 0;
                foreach ($query as $name => $value) {
                    if ($value == $data[$i][$name]) {
                        ++$add;
                    }
                }
                if ($add === count($query)) {
                    $return[] = static::model($data[$i]);
                }

                if (count($return) === $limit) {
                    break;
                }
            }
        }
        return $return;
    }

    /**
     * Get model
     *
     * @param array $params
     * @return \phantomd\ShopCart\modules\base\BaseModel
     */
    public static function model($params = [])
    {
        if (empty($params)) {
            $params = [];
        } else {
            if (false === is_array($params)) {
                $params = ['id' => $params];
            }
        }

        $class = get_called_class();
        return new $class($params);
    }

    /**
     * Get fields from current model
     *
     * @return array
     */
    public function getFields()
    {
        $return = [];
        if ($rules  = $this->rules()) {
            foreach ($rules as $rule) {
                if (isset($rule[0]) && is_array($return)) {
                    $fields  = array_flip($rule[0]);
                    $default = null;
                    if (isset($rule[1]) && isset($rule[2]) && 'default' === $rule[1]) {
                        $default = $rule[2];
                    }
                    foreach ($fields as $key => $value) {
                        $fields[$key] = $default;
                    }
                    $return = array_merge($return, $fields);
                }
            }
        }
        return $return;
    }

    /**
     * Get model data as array
     *
     * @return array
     */
    public function asArray()
    {
        $return = [];
        foreach ($this->fields as $name => $value) {
            $return[$name] = $this->{$name};
        }

        return $return;
    }

}
