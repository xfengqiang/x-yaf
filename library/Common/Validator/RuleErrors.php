<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-27 08:04
 */

namespace Common\Validator;

abstract class RuleErrors {
    /**
     * @param $type
     * @return string
     */
    static public function errFormats($type) {
        $messages = [
            Rule::ERR_ENUM => '%s填写不正确',
            Rule::ERR_MIN => '%s不能小于%d',
            Rule::ERR_MAX => '%s不能大于%d',
            Rule::ERR_MIN_LEN => '%s的长度不能小于%d',
            Rule::ERR_MAX_LEN => '%s的长度不能大于于%d',
            Rule::ERR_REQUIRED => '%s未填写',
            Rule::ERR_REGEX => '%s的格式填写不正确',
            Rule::ERR_RANGE => '%s需要在%s-%s之间',
        ];
        return isset($messages[$type]) ? $messages[$type] : '%s填写不正确';
    }
} 