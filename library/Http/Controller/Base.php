<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-26 21:44
 */

namespace Http\Controller;

use Common\Exception\AppException;
use Common\Http\Response;
use Common\Validator\Rule;
use Common\Validator\RulesInterface;

abstract class Base extends \Yaf_Controller_Abstract{
    public function init(){}

    /**
     * @param $rules
     * @param $name
     * @return \Common\Validator\Rule
     */
    private function getRules($rules, $name){
         $rules = isset($rules[$name]) ? $rules[$name] : null;
        if (!$rules){
            $rules = [new Rule('str', $name)];
        }
        return $rules;
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
                throw new AppException($rule->errMsg(), Error::ERR_INVALID_PARAM);
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

    public function enableView(){
        \Yaf_Dispatcher::getInstance()->autoRender(true)->enableView();
    }

    
    public function disableView(){
        \Yaf_Dispatcher::getInstance()->autoRender(false)->disableView();
    }

    public function addResource() {
        $r = $this->getRequest();
        $page_js = strtolower('app/'.$r->getControllerName().'/'.$r->getActionName());
        $this->getView()->assign('page_js', $page_js);
        $js_debug = $this->getRequest()->get('js_debug', '0');
        $this->getView()->assign('js_debug', $js_debug);
    }
    
    public function renderView($data=[]){
        if($data) {
            foreach($data as $k=>$v) {
                $this->getView()->assign($k, $v);
            }
        }
    }
    public function returnOk($data = array(), $msg='', $code=0) {
        $ret = array('code'=>$code, 'data'=>$data, 'msg'=>$msg);
        $this->getResponse()->setBody(Response::json($ret));
    }

    public function returnError($code, $msg=''){
        throw new AppException($msg, $code);
    }
    
} 

