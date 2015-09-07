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
    private function getRules($rules, $name){
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
            $rule = $this->getRules($rules, $name);
            $value = $r->get($name);
            $this->checkRules($value, $rule, true);
            $ret[$name] = $value;
        }

        foreach($optional as $name) {
            $rule = $this->getRules($rules, $name);
            $value = $r->get($name);
            $this->checkRules($value, $rule, false);
            $ret[$name] = $value;
        }
        return $ret;
    }
    
    protected function checkRules(&$value, $rules, $required) {
        if(!$rules) {
            return true;
        }
        foreach($rules as $rule) {
            $err = $rule->required($required)->validate($value);
            if ($err && $required) {
                throw new \Exception($rule->errMsg(), Error::ERR_INVALID_PARAM);
            }
            $value = $rule->getValue();
        }
        return true;
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

    /**
     * 获取js rule
     * @param array          $params
     * @param RulesInterface $ruleObj
     * @return array
     */
    public function getRuleJs(array $params, RulesInterface $ruleObj) {
        $config = ['rules'=>null, 'messages'=>null];
        $rules = $ruleObj->getRules();
        foreach($params as $name=>$required) {
            $rs = isset($rules[$name]) ? $rules[$name] : null;
            if (!$rs) {
                continue;
            }

            foreach($rs as $rule) {
                $rule->required($required)->getJsRule($config, $name);
            }
        }

        return $config;
    }
    
    public function disableView(){
        \Yaf_Dispatcher::getInstance()->autoRender(false)->disableView();
    }
} 

