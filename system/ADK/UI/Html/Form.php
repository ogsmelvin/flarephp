<?php

namespace ADK\UI\Html;

use ADK\Adk as A;
use \Exception;

if(!function_exists('adk_encrypt')){
    A::mvc()->helper('encrypt');
}

/**
 * 
 * @author anthony
 * 
 */
class Form
{
    /**
     * 
     * @var string
     */
    const POST = 'post';
    const GET = 'get';

    /**
     * 
     * @var string
     */
    const VALIDATE_EMAIL = '_validateEmail';
    const VALIDATE_NUMBER = '_validateNumber';
    const VALIDATE_NOT_EMPTY = '_validateNotEmpty';

    /**
     * 
     * @var string
     */
    const FILTER_HTML = '_filterHtml';

    /**
     * 
     * @var string
     */
    private $_name;

    /**
     * 
     * @var string
     */
    private $_method;

    /**
     * 
     * @var array
     */
    private $_data;

    /**
     * 
     * @var string
     */
    private $_token;

    /**
     * 
     * @var string
     */
    private $_tokenKey;

    /**
     * 
     * @var boolean
     */
    private $_submitted = false;

    /**
     * 
     * @var array
     */
    private $_errorInfo = array();

    /**
     * 
     * @var array
     */
    private $_filters = array();

    /**
     * 
     * @var array
     */
    private $_validators = array();

    /**
     * 
     * @var array
     */
    private static $_formValidators = array(
        self::VALIDATE_NUMBER, self::VALIDATE_EMAIL, self::VALIDATE_NOT_EMPTY
    );

    /**
     * 
     * @var array
     */
    private static $_formFilters = array(
        self::FILTER_HTML
    );

    /**
     * 
     * @param string $name
     * @param string $method
     */
    public function __construct($name, $method = self::POST)
    {
        $name = str_replace(' ', '_', $name);
        $method = strtolower($method);
        if(!in_array($method, array(self::POST, self::GET))){
            throw new Exception("Request method passed is not recognized");
        }
        $this->_name = $name;
        $this->_method = $method;
        $this->_data = A::$request->{$method}($name, array());
        $this->_createToken();
    }

    /**
     * 
     * @param boolean $change
     * @return void
     */
    private function _createToken($change = false)
    {
        $this->_tokenKey = '_adk_form_token_'.$this->_name;
        if(!isset(A::$session->{$this->_tokenKey}) || $change === true){
            $this->_token = md5(uniqid());
            A::$session->{$this->_tokenKey} = $this->_token;
        } else {
            $this->_token = A::$session->{$this->_tokenKey};
        }
        return;
    }

    /**
     * 
     * @return array
     */
    public function submit()
    {
        if($this->_submitted){
            throw new Exception("Form '{$this->_name}' submitted already");
        }
        $data = array();
        if($this->_filters){
            $data = $this->filter();
        } else {
            $data = $this->getData();
        }
        $this->validate();
        $this->clearToken();
        $this->_submitted = true;
        return $data;
    }

    /**
     * 
     * @return \ADK\UI\Html\Form
     */
    public function reset()
    {
        $this->_submitted = false;
        return $this->changeToken();
    }

    /**
     * 
     * @return \ADK\UI\Html\Form
     */
    public function clearToken()
    {
        unset(A::$session->{$this->_tokenKey}, $this->_token);
        return $this;
    }

    /**
     * 
     * @param string $name
     * @param string|array $filter
     * @return \ADK\UI\Html\Form
     */
    public function setFilter($name, $filter)
    {
        $filter = $this->_cleanFilterName($filter);
        if(is_string($filter)){
            if(!in_array($filter, self::$_formFilters)){
                throw new Exception("Filter name doesn't exists");
            }
            $filter = (array) $filter;
        } else if(is_array($filter)){
            foreach($filter as $v){
                if(!in_array($v, self::$_formFilters)){
                    throw new Exception("Filter name doesn't exists");
                }
            }
        } else {
            throw new Exception("Invalid data type for filter");
        }
        if(!isset($this->_filters[$name])){
            $this->_filters[$name] = array();
        }
        $this->_filters[$name] = array_unique(array_merge($this->_filters[$name], $filter));
        return $this;
    }

    /**
     * 
     * @param array $filters
     * @return \ADK\UI\Html\Form
     */
    public function setFilters(array $filters)
    {
        foreach($filters as &$filter){
            $filter = $this->_cleanFilterName($filter);
            if(!in_array($filter, self::$_formFilters)){
                throw new Exception("Filter name doesn't exists");
            }
        }
        foreach($this->_data as $key => $val){
            if(!isset($this->_filters[$key])){
                $this->_filters[$key] = array();
            }
            $this->_filters[$key] = array_unique(array_merge($this->_filters[$key], $filters));
        }
        return $this;
    }

    /**
     * 
     * @param string $name
     * @return string|array
     */
    private function _cleanFilterName($name)
    {
        if(is_string($name)){
            $name = '_filter'.str_replace(' ', '', ucwords(strtolower($name)));
        } else if(is_array($name)){
            foreach($name as &$n){
                $n = '_filter'.str_replace(' ', '', ucwords(strtolower($n)));
            }
        }
        return $name;
    }

    /**
     * 
     * @return \ADK\UI\Html\Form
     */
    public function changeToken()
    {
        $this->_createToken(true);
        return $this;
    }

    /**
     * 
     * @return boolean
     */
    public function isGet()
    {
        return $this->_method === self::GET;
    }

    /**
     * 
     * @return boolean
     */
    public function isPost()
    {
        return $this->_method === self::POST;
    }

    /**
     * 
     * @return boolean
     */
    public function isAjax()
    {
        return A::$request->isAjax();
    }

    /**
     * 
     * @param string $name
     * @param string $filter
     * @return array
     */
    public function filter($name = null, $filter = null)
    {
        if($name){
            if(!$filter){
                throw new Exception("Must specify filter name");
            }
            return $this->setFilter($name, $filter)->filter();
        }

        if(!$name && !$filter){
            if(!$this->_filters){
                throw new Exception("Must set form validations");
            }
            foreach($this->_filters as $key => $val){
                foreach($val as $v){
                    $this->_data[$key] = $this->{$v}($this->_data[$key]);
                }
                
            }
        }

        return $this->getData();
    }

    /**
     * 
     * @return string
     */
    private function _filterHtml($value)
    {
        return htmlentities(strip_tags($value));
    }

    /**
     * 
     * @param string $name
     * @param string $validator
     * @return \ADK\UI\Html\Form
     */
    public function setValidator($name, $validator)
    {
        $validator = $this->_cleanValidatorName($validator);
        if(is_string($validator)){
            if(!in_array($validator, self::$_formValidators)){
                throw new Exception("Validator name doesn't exists");
            }
            $validator = (array) $validator;
        } else if(is_array($validator)){
            foreach($validator as $v){
                if(!in_array($v, self::$_formValidators)){
                    throw new Exception("Validator name doesn't exists");
                }
            }
        } else {
            throw new Exception("Invalid data type for validator");
        }
        if(!isset($this->_validators[$name])){
            $this->_validators[$name] = array();
        }
        $this->_validators[$name] = array_unique(array_merge($this->_validators[$name], $validator));
        return $this;
    }

    /**
     * 
     * @param array $validators
     * @return \ADK\UI\Html\Form
     */
    public function setValidators(array $validators)
    {
        foreach($validators as &$validator){
            $validator = $this->_cleanValidatorName($validator);
            if(!in_array($validator, self::$_formValidators)){
                throw new Exception("Validator name doesn't exists");
            }
        }
        foreach($this->_data as $key => $val){
            if(!isset($this->_validators[$key])){
                $this->_validators[$key] = array();
            }
            $this->_validators[$key] = array_unique(array_merge($this->_validators[$key], $validators));
        }
        return $this;
    }

    /**
     * 
     * @param string|array $name
     * @return string|array
     */
    private function _cleanValidatorName($name)
    {
        if(is_string($name)){
            $name = '_validate'.str_replace(' ', '', ucwords(strtolower($name)));
        } else if(is_array($name)){
            foreach($name as &$n){
                $n = '_validate'.str_replace(' ', '', ucwords(strtolower($n)));
            }
        }
        return $name;
    }

    /**
     * 
     * @param string $name
     * @return string
     */
    private function _cleanName($name)
    {
        return ucfirst(str_replace('_', ' ', strtolower($name)));
    }

    /**
     * 
     * @param string $name
     * @param string $validator
     * @return boolean
     */
    public function validate($name = null, $validator = null)
    {
        if($name){
            if(!$validator){
                throw new Exception("Must specify validator name");
            }
            return $this->setValidator($name, $validator)->validate();
        }

        if(!$this->validToken()){
            return false;
        }

        if(!$name && !$validator){
            if(!$this->_validators){
                throw new Exception("Must set form validations");
            }
            foreach($this->_validators as $key => $val){
                foreach($val as $v){
                    if(!$this->{$v}($key)){
                        return false;
                    }
                }
            }
        }
        
        return true;
    }

    /**
     * 
     * @return boolean
     */
    public function validToken()
    {
        if(strcmp($this->getData('__token'), $this->getToken()) !== 0){
            $this->_errorInfo['__token'] = 'Invalid token';
            return false;
        }
        if(strpos(A::$request->server('HTTP_REFERER'), A::$uri->getBaseUrl()) !== 0){
            $this->_errorInfo['__token'] = 'Invalid referer';
            return false;
        }
        return true;
    }

    /**
     * 
     * @param string $name
     * @return boolean
     */
    private function _validateEmail($name)
    {
        if(!filter_var($this->getData($name), FILTER_VALIDATE_EMAIL)){
            $this->_errorInfo[$name] = 'Invalid email';
            return false;
        }
        return true;
    }

    /**
     * 
     * @param string $name
     * @return boolean
     */
    private function _validateNumber($name)
    {
        if(!is_numeric($this->getData($name))){
            $this->_errorInfo[$name] = $this->_cleanName($name).' must be numeric';
            return false;
        }
        return true;
    }

    /**
     * 
     * @param string $name
     * @return boolean
     */
    private function _validateNotEmpty($name)
    {
        $val = $this->getData($name);
        if(empty($val)){
            $this->_errorInfo[$name] = $this->_cleanName($name).' must not empty';
            return false;
        }
        return true;
    }

    /**
     * 
     * @param string $key
     * @return string
     */
    public function getData($key = null, $default = null)
    {
        if($key === null){
            return $this->_data;
        }
        return isset($this->_data[$key]) ? $this->_data[$key] : $default;
    }

    /**
     * 
     * @param string $name
     * @return \ADK\UI\Html\Form
     */
    public function setData($key, $value)
    {
        if($key === '__token'){
            throw new Exception("{$key} cannot be override");
        }
        $this->_data[$key] = $value;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getValidators()
    {
        return $this->_validators;
    }

    /**
     * 
     * @return string|array
     */
    public function getError($key = null)
    {
        if($key === null){
            return $this->_errorInfo;
        }
        return isset($this->_errorInfo[$key]) ? $this->_errorInfo[$key] : null;
    }

    /**
     * 
     * @return string
     */
    public function getToken()
    {
        return isset($this->_token) ? $this->_token : null;
    }

    /**
     * 
     * @return string
     */
    public function renderToken()
    {
        return "<input type=\"hidden\" name=\"{$this->_name}[__token]\" value=\"{$this->getToken()}\">";
    }

    /**
     * 
     * @return string
     */
    public function getName($name = null)
    {
        if($name !== null){
            return $this->_name.'['.$name.']';
        }
        return $this->_name;
    }
}
