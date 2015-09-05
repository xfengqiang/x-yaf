<?php
/**
 * @name IndexController
 * @author frank.xu
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
use \Common\Validator\Rule;
class AccountController extends \Http\Controller\Web {
    public function loginAction(){
    }
    public function registerAction(){
    }
    public function register2Action(){
        $params = array(
            'username'=>1,
            'email'=>1,
            'password'=>1,
            'gender'=>1,
            'accept_rule'=>1,
        );
        $user = new \apps\common\models\User();
        $rules = $user->getRules();

        $jsRules = ['rules'=>null, 'messages'=>null];
        foreach($params as $name=>$required) {
            $rules = isset($rules[$name]) ? $rules[$name] : null;
            if ($rules) {
                $this->getRuleJs($jsRules, $name, $rules, $required);
            }
        }
        $this->renderView(['js_config'=>$jsRules]);
    }
    public function getRuleJs(&$config , $name, array $rules, $required) {
        $config['rules'][$name]['required'] = (bool)$required;
       if($rules) {
           if($required) {
               $config['messages'][$name]['required'] = $rules[0]->errMsgForType(Rule::ERR_REQUIRED);
           }
       }else{
           $config['messages'][$name]['required'] = "{$name}是必填参数";
       }
        
        foreach($rules as $rule){
            switch($rule->type) {
                case Rule::TYPE_BOOL:
                case Rule::TYPE_INT:
                case Rule::TYPE_FLOAT:
                    $config['messages'][$name]['number'] = true;
                    
                    if($rule->min !== null && $rule->max!==null){
                        $config['messages'][$name]['range'] = $rule->errMsgForType(Rule::ERR_RANGE);
                        $config['rules'][$name]['range'] =  [$rule->min, $rule->max];
                    }else if($rule->min !== null) {
                        $config['rules'][$name]['min'] =  $rule->min;
                        $config['messages'][$name]['min'] = $rule->errMsgForType(Rule::ERR_MIN);
                    }else if($rule->max!==null){
                        $config['rules'][$name]['max'] =  $rule->max;
                        $config['messages'][$name]['max'] = $rule->errMsgForType(Rule::ERR_MAX);
                    }
                    break;
                case Rule::TYPE_STR:
                    if($rule->min !== null && $rule->max!==null){
                        $config['messages'][$name]['rangelength'] = $rule->errMsgForType(Rule::ERR_RANGE_STR);
                        $config['rules'][$name]['rangelength'] =  [$rule->min, $rule->max];
                    }else  if($rule->min !== null) {
                        $config['rules'][$name]['minlength'] =  $rule->min;
                        $config['messages'][$name]['minlength'] = $rule->errMsgForType(Rule::ERR_MIN);
                    }else if($rule->max!==null){
                        $config['rules'][$name]['maxlength'] =  $rule->max;
                        $config['messages'][$name]['maxlength'] = $rule->errMsgForType(Rule::ERR_MAX);
                    }
                    break;
            }
            
                switch($rule->strType) {
                    case 'email':
                        $config['rules'][$name]['email'] = true;
                        $config['messages'][$name]['email'] = $rule->errMsgForType(Rule::ERR_REGEX);
                        break;
                    case 'url':
                        $config['rules'][$name]['url'] = true;
                        $config['messages'][$name]['url'] = $rule->errMsgForType(Rule::ERR_REGEX);
                        break;
                    case 'regex':
                        //TODO
                        $config['messages'][$name]['regex'] = $rule->errMsgForType(Rule::ERR_REGEX);
                        break;
                    case 'phone':
                        $config['messages'][$name]['phone'] = $rule->errMsgForType(Rule::ERR_REGEX);
                        //TODO
                        break;
                }
                
        }
    }
    
    public function detailAction(){
        $this->disableView();
        $id = $this->requiredParams(['id']);
        echo "hello {$id['id']}";
    }
    public function logoutAction(){
        Yaf_Session::getInstance()->del('username');
        $this->redirect('/account/login');
    }
    public function vcodeAction(){
        Yaf_Dispatcher::getInstance()->autoRender(false)->disableView();
        \Common\Util\VerifyCode::createCode();
    }
}
