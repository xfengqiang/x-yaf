<?php
/**
 * @Autor: frank
 * @Date : 2015-05-24 22:30
 */

namespace Common\Validator;

/**
 * Class Rule
 *
 * @package Common\Validator
 */


class Rule {
    const TYPE_INT   = 'int';
    const TYPE_FLOAT = 'bool';
    const TYPE_STR   = 'str';
    const TYPE_BOOL  = 'bool';

    const ERR_NONE      = '';
    const ERR_MIN       = 'min';
    const ERR_MAX       = 'max';
    const ERR_MIN_LEN   = 'min_len';
    const ERR_MAX_LEN   = 'max_len';
    const ERR_REGEX     = 'regex';
    const ERR_ENUM      = 'enum';
    const ERR_RANGE     = 'range';
    const ERR_RANGE_STR = 'range_str';
    const ERR_REQUIRED  = 'required';

    public $name = '';
    public $title = '';

    private  $type = null;
    private $min = null;
    private $max = null;
    private $enumValues = null;
    private $regex = null;
    private $jsRegex = null;
    private $default = null;
    private $trim = true;
    private $required = false;
    private $equalToParam = null;

    private $errType = null;
    private $strType = null;
    private $errMsgs = [];
    private $errArgDisabled = false;

    public $value = null;


    /**
     * @param      $type
     * @param null $title
     * @internal param null $name
     * @internal param $value
     * @return Rule
     */
    static public function rule($type = null, $title = null) {
        $rule = new Rule($type, $title);

        return $rule;
    }

    public function __construct($type = null, $title = null) {
        $this->type  = $type;
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
    public function name($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * @return $this
     */
    public function phone() {
        $this->strType = 'phone';
        $this->regex   = '/^(\+?86)?1[3578]\d{9}$/';
        $this->jsRegex = $this->regex;
        return $this;
    }

    /**
     * @return $this
     */
    public function email() {
        $this->strType = 'email';
        $this->regex   = '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/';
        $this->jsRegex = $this->regex;

        return $this;
    }

    /**
     * @return $this
     */
    public function url() {
        $this->strType = 'url';
        $this->regex   = '/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i';
        $this->jsRegex = $this->regex;

        return $this;
    }

    /**
     * @param        $regex
     * @param string $jsRegex
     * @return $this
     */
    public function regex($regex, $jsRegex = '') {
        $this->strType = 'regex';
        $this->regex   = $regex;
        $this->jsRegex = $jsRegex;

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
    public function max($max) {
        $this->max = $max;

        return $this;
    }

    /**
     * @param $min
     * @param $max
     * @return Rule
     */
    public function range($min, $max) {
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
    public function defaultValue($v) {
        $this->default = $v;

        return $this;
    }

    /**
     * @param $v
     * @return $this
     */
    public function equalParam($v) {
        $this->equalToParam = $v;

        return $this;
    }

    /**
     * @param $value
     * @return string
     */
    public function validate($value) {
        if (!$this->required && !$value) {
            $this->value   = $this->default;
            $this->errType = self::ERR_NONE;

            return $this->errType;
        }

        $value = ($value === null) ? $this->default : $value;

        switch ($this->type) {
            case self::TYPE_BOOL:
                $value = boolval($value);
                break;
            case self::TYPE_INT:
            case self::TYPE_FLOAT:
                $value = $this->type == self::TYPE_INT ? intval($value) : floatval($value);
                if ($this->min !== null && $value < $this->min) {
                    $this->errType = self::ERR_MIN;
                    break;
                }
                if ($this->max !== null && $value > $this->max) {
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

                if ($this->max !== null && $len > $this->max) {
                    $this->errType = self::ERR_MAX_LEN;
                    break;
                }
                if ($this->min !== null && $len < $this->min) {
                    $this->errType = self::ERR_MIN_LEN;
                    break;
                }

                if ($this->regex !== null && !preg_match($this->regex, $value)) {
                    $this->errType = self::ERR_REGEX;
                    break;
                }
                break;
            default:
                break;
        }

        if ($this->errType == self::ERR_MIN || $this->errType == self::ERR_MAX) {
            if ($this->min !== null && $this->max !== null) {
                $this->errType = ($this->type == 'str') ? self::ERR_RANGE_STR : self::ERR_RANGE;
            }
        }

        if (!$this->errType && $this->enumValues && !in_array($value, $this->enumValues)) {
            $this->errType = self::ERR_ENUM;
        }
        $this->value = $this->errType ? $this->default : $value;

        return $this->errType;
    }

    /**
     * @param $title
     * @return Rule
     */
    public function title($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param null $format
     * @param      $errType
     * @param bool $disableErrArg
     * @internal param bool $enableArg
     * @return Rule
     */
    public function errFormat($format, $errType, $disableErrArg = false) {
        $this->errMsgs[$errType] = $format;
        $this->errArgDisabled    = $disableErrArg;

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
            return '';
        }

        $format = (isset($this->errMsgs[$errType])) ? $this->errMsgs[$errType] : RuleErrors::errFormats($errType);

        $title = ($this->title == null) ? $this->name : $this->title;
        $args  = [$format];
        if (!$this->errArgDisabled) {
            array_push($args, $title);
            switch ($errType) {
                case self::ERR_REQUIRED:
                case self::ERR_REGEX:
                case self::ERR_ENUM:
                    break;
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
            }
        }

        return call_user_func_array('sprintf', $args);
    }

    /**
     * @return string
     */
    public function errMsg() {
        $this->errMsgForType($this->errType);
    }

    /**
     * 生成js 规则
     * @param $config
     * @param $name
     */
    public function getJsRule(&$config, $name) {
        $config['rules'][$name]['required'] = $this->required;
        if ($this->required) {
            $config['messages'][$name]['required'] = $this->errMsgForType(Rule::ERR_REQUIRED);
        }

        switch ($this->type) {
            case Rule::TYPE_BOOL:
            case Rule::TYPE_INT:
            case Rule::TYPE_FLOAT:
                $config['messages'][$name]['number'] = true;

                if ($this->min !== null && $this->max !== null) {
                    $config['messages'][$name]['range'] = $this->errMsgForType(Rule::ERR_RANGE);
                    $config['rules'][$name]['range']    = [$this->min, $this->max];
                } else if ($this->min !== null) {
                    $config['rules'][$name]['min']    = $this->min;
                    $config['messages'][$name]['min'] = $this->errMsgForType(Rule::ERR_MIN);
                } else if ($this->max !== null) {
                    $config['rules'][$name]['max']    = $this->max;
                    $config['messages'][$name]['max'] = $this->errMsgForType(Rule::ERR_MAX);
                }
                break;
            case Rule::TYPE_STR:
                if ($this->min !== null && $this->max !== null) {
                    $config['messages'][$name]['rangelength'] = $this->errMsgForType(Rule::ERR_RANGE_STR);
                    $config['rules'][$name]['rangelength']    = [$this->min, $this->max];
                } else if ($this->min !== null) {
                    $config['rules'][$name]['minlength']    = $this->min;
                    $config['messages'][$name]['minlength'] = $this->errMsgForType(Rule::ERR_MIN);
                } else if ($this->max !== null) {
                    $config['rules'][$name]['maxlength']    = $this->max;
                    $config['messages'][$name]['maxlength'] = $this->errMsgForType(Rule::ERR_MAX);
                }
                break;
        }

        if ($this->enumValues) {
            $config['rules'][$name]['enum']    = $this->enumValues;
            $config['messages'][$name]['enum'] = $this->errMsgForType(Rule::ERR_ENUM);
        }

        switch ($this->strType) {
            case 'email':
                $config['rules'][$name]['email']    = true;
                $config['messages'][$name]['email'] = $this->errMsgForType(Rule::ERR_REGEX);
                break;
            case 'url':
                $config['rules'][$name]['url']    = true;
                $config['messages'][$name]['url'] = $this->errMsgForType(Rule::ERR_REGEX);
                break;
            case 'regex':
                $config['rules'][$name]['regex']    = $this->jsRegex;
                $config['messages'][$name]['regex'] = $this->errMsgForType(Rule::ERR_REGEX);
                break;
            case 'phone':
                $config['rules'][$name]['regex']    = $this->jsRegex;
                $config['messages'][$name]['phone'] = $this->errMsgForType(Rule::ERR_REGEX);
                break;
        }
        
        if($this->equalToParam) {
            $config['rules'][$name]['equalTo'] = "[name={$this->equalToParam}]";
        }
    }
}