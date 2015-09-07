<?php
/**
 * @name SampleModel
 * @desc sample数据获取类, 可以访问数据库，文件，其它系统等
 * @author frank.xu
 */

namespace apps\common\models;
use Common\Validator\Rule;
use Common\Validator\RulesInterface;

class RuleTest  implements RulesInterface{
    public function getRules($params=[]){
        $allRules =  [
            'username'=>[Rule::rule('str', '姓名')->min(6)->max(20)],
            'desc'=>[Rule::rule('str', '个性描述')->max(10)],
            'email'=>[Rule::rule('str', '邮箱')->email()],
            'height'=>[Rule::rule('str', '身高')->regex('/[\d]-[\d]/i', '/\\d-\\d/')],
            'age'=>[Rule::rule('int', '年龄')->min(1)->max(100)->errFormat('%s不能小于%d, 不能大于%s', Rule::ERR_RANGE)],
            'gender_enum'=>[Rule::rule('str', '性别枚举')->enum(['m', 'f'])],
            'gender'=>[Rule::rule('str', '性别')->enum(['m', 'f'])],
            'password'=>[Rule::rule('str', '密码')->min(6)],
            'password_confirm'=>[Rule::rule('str', '确认密码')->equalParam('password')],
            'phone'=>[Rule::rule('str', '手机')->phone()],
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
