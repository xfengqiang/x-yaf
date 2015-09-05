<?php
/**
 * @name SampleModel
 * @desc sample数据获取类, 可以访问数据库，文件，其它系统等
 * @author frank.xu
 */

namespace apps\common\models;
use Common\Validator\Rule;
use Common\Validator\RulesInterface;

class User extends \Common\Mysql\Model implements RulesInterface{
    public function getDb(){
        return "xtest";
    }
    
    public function getRules($params=[]){
        $allRules =  [
            'username'=>[Rule::rule('str', '姓名')->min(6)->max(20)],
            'email'=>[Rule::rule('str', '邮箱')->email()],
            'password'=>[Rule::rule('str', '密码')->min(6)],
            'age'=>[Rule::rule('int', '年龄')->min(1)->max(100)->errFormat("%s不能大于%d, 不能小于%d")],
            'phone'=>[Rule::rule('str', '手机')->phone()],
            'height'=>[Rule::rule('str', '身高')->regex('/[\d]-[\d]/i')],
            'gender'=>[Rule::rule('str', '性别')->enum(['m', 'f'])],
        ];
        if(!$params) {
            return $allRules;
        }
        
        $ret = [];
        foreach($params as $name) {
            if(isset($allRules[$name])){
                $ret[$name] = $allRules[$name];
            }
        }
        return $ret;
    }
    
}
