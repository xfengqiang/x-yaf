<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-26 21:44
 */

namespace Http\Controller;

use Common\Validator\RulesInterface;
use Common\Validator\Rule;

abstract class Base extends \Yaf_Controller_Abstract{
    public function init(){}


    /**
     * @param $rules
     * @param $name
     * @return \Common\Validator\Rule
     */
    private function getRule($rules, $name){
        $rule =  isset($rules[$name]) ? $rules[$name] : null;
        if ($rule){
            $rule->name($name);
        }
        return $rule;
    }
    /**
     * @param array          $required
     * @param array          $optional
     * @param RulesInterface $rules
     * @throws \Exception
     * @return array
     */
    public function params(array $required, array $optional, RulesInterface $rules=null) {
        $ret = [];
        $r = $this->getRequest();
        $rules = $rules ? $rules->getRules() : [];
        foreach($required as $name) {
            $rule = $this->getRule($rules, $name);
            $value = $r->get($name);
            if ($rule) {
                $err = $rule->required(true)->validate($value);
                if ($err) {
                    throw new \Exception($rule->errMsg(), Error::ERR_INVALID_PARAM);
                }
                $value = $rule->getValue();
            }
            $ret[$name] = $value;
        }

        foreach($optional as $name) {
            $rule = $this->getRule($rules, $name);
            
            $value = $r->get($name);
            if ($rule) {
                $rule->required(false)->validate($value);
                $value = $rule->getValue();
            }
            $ret[$name] = $value;
        }
        return $ret;
    }

    /**
     * @param                                                                  $params
     * @param \Common\Validator\RulesInterface $rules
     * @return array
     */
    public function requiredParams($params,  RulesInterface $rules=null) {
        return $this->params($params, [], $rules);
    }

    /**
     * @param                $params
     * @param RulesInterface $rules
     * @return array
     */
    public function optionParams($params, RulesInterface $rules=null) {
        return $this->params([], $params, $rules);
    }
    
    public function disableView(){
        \Yaf_Dispatcher::getInstance()->autoRender(false)->disableView();
    }
} 

