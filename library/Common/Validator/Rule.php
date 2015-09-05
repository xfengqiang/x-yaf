<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-24 22:30
 */

namespace Common\Validator;

/**
 * Class Rule
 *              
 * @package Common\Validator
 */


class Rule {
    const TYPE_INT = 'int';
    const TYPE_FLOAT = 'bool';
    const TYPE_STR = 'str';
    const TYPE_BOOL = 'bool';

    const ERR_NONE = '';
    const ERR_MIN = 'min';
    const ERR_MAX = 'max';
    const ERR_MIN_LEN = 'min_len';
    const ERR_MAX_LEN = 'max_len';
    const ERR_REGEX = 'regex';
    const ERR_ENUM = 'enum';
    const ERR_RANGE = 'range';
    const ERR_RANGE_STR = 'range_str';
    const ERR_REQUIRED = 'required';

    public $name = '';
    public $title = '';
    
    public $type = null;
    public $min = null;
    public $max = null;
    public $enumValues = null;
    public $regex = null;
    public $default = null;
    public $trim = true;
    public $required = false;

    public $errType = null;
    public $strType = null;
    public $errFormat = null;

    public $value = null;

    /**
     * @param      $type
     * @param null $title
     * @internal param null $name
     * @internal param $value
     * @return Rule
     */
    static public function rule($type=null, $title=null) {
        $rule = new Rule($type, $title);
        return $rule;
    }
    
    public function __construct($type=null, $title=null){
        $this->type = $type;
        $this->title = $title;
    }

    /**
     * @param $type
     * @return Rule
     */
    public function type($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function name($name){
        $this->name = $name;
        return $this;
    }

    /**
     * @return $this
     */
    public function phone(){
        $this->strType = 'phone';
        $this->regex = '/^(\+?86)?1[3578]\d{9}$/';
        return $this;
    }

    /**
     * @return $this
     */
    public function email(){
        $this->strType = 'email';
        $this->regex = '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/';
        return $this;
    }

    /**
     * @return $this
     */
    public function url(){
        $this->strType = 'url';
        $this->regex = '/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i';
        return $this;
    }

    /**
     * @param $regex
     * @return $this
     */
    public function regex($regex){
        $this->strType = 'regex';
        $this->regex = $regex;
        return $this;
    }
    
    /**
     * @param $min
     * @return Rule
     */
    public function min($min) {
        $this->min = $min;
        return $this;
    }

    /**
     * @param $max
     * @return Rule
     */
    public function max($max){
        $this->max = $max;
        return $this;
    }

    /**
     * @param $min
     * @param $max
     * @return Rule
     */
    public function range($min, $max){
        $this->min = $min;
        $this->max = $max;
        return $this;
    }

    /**
     * @param $values
     * @return Rule
     */
    public function enum($values) {
        $this->enumValues = $values;
        return $this;
    }

    /**
     * @param $required
     * @return $this
     */
    public function required($required) {
        $this->required = $required;
        return $this;
    }

    /**
     * @param $v
     * @return $this
     */
    public function defaultValue($v){
        $this->default = $v;
        return $this;
    }

    /**
     * @param $value
     * @return string
     */
    public function validate($value) {
        if (!$this->required && !$value){
            $this->value = $this->default;
            $this->errType = self::ERR_NONE;
            return $this->errType;
        }
        
        $value = ($value===null) ? $this->default : $value;
        
        switch ($this->type) {
            case self::TYPE_BOOL:
                $value = boolval($value);
                break;
            case self::TYPE_INT:
            case self::TYPE_FLOAT:
                $value = $this->type==self::TYPE_INT ? intval($value) : floatval($value);
                if ($this->min !== null && $value < $this->min) {
                    $this->errType = self::ERR_MIN;
                    break;
                }
                if ($this->max !==null && $value > $this->max) {
                    $this->errType = self::ERR_MAX;
                    break;
                }
             break;
            case self::TYPE_STR:
                if ($this->trim) {
                    $value = trim($value);
                }
             
                if ($this->required && !$value) {
                    $this->errType = self::ERR_REQUIRED;
                    break;
                }
                
                $len = mb_strlen($value, "utf8");
                
                if ($this->max!==null && $len > $this->max) {
                    $this->errType = self::ERR_MAX_LEN;
                    break;
                }
                if ($this->min !==null && $len < $this->min){
                    $this->errType = self::ERR_MIN_LEN;
                    break;
                }
                
                if ($this->regex !== null && !preg_match($this->regex, $value)){
                    $this->errType = self::ERR_REGEX;
                    break;
                }
                break;
            default:
                break;
        }
        
        if ($this->errType == self::ERR_MIN || $this->errType == self::ERR_MAX){
            if ($this->min!==null && $this->max!==null){
                $this->errType = ($this->type=='str') ? self::ERR_RANGE_STR : self::ERR_RANGE;
            }    
        }
        
        if (!$this->errType && $this->enumValues && !in_array($value, $this->enumValues)){
            $this->errType = self::ERR_ENUM;
        }
        $this->value = $this->errType ? $this->default : $value;
        return $this->errType;
    }

    /**
     * @param $title
     * @return Rule
     */
    public function title($title){
        $this->title = $title;
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * @param null $format
     * @return Rule
     */
    public function errFormat($format=null){
        $this->errFormat = $format;
        return $this;
    }
    
    /**
     * @return string
     */
    public function error() {
        return $this->errType;
    }

    public function errMsgForType($errType) {
        if ($errType == self::ERR_NONE) {
            return  '';
        }

        $format = ($this->errFormat==null) ? RuleErrors::errFormats($errType) : $this->errFormat;

        $title = ($this->title==null)? $this->name : $this->title;
        $args = [$format, $title];
        switch ($errType){
            case self::ERR_MIN:
            case self::ERR_MIN_LEN:
                array_push($args, $this->min);
                break;
            case self::ERR_MAX:
            case self::ERR_MAX_LEN:
                array_push($args, $this->max);
                break;
            case self::ERR_RANGE:
            case self::ERR_RANGE_STR:
                array_push($args, $this->min, $this->max);
                break;
            case self::ERR_REQUIRED:
            case self::ERR_REGEX:
            case self::ERR_ENUM:
                break;
        }

        return call_user_func_array('sprintf', $args);
    }
    /**
     * @return string
     */
    public function errMsg() {
        $this->errMsgForType($this->errType);
    }
} 