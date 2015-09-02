<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-24 09:57
 */

namespace Common\String;

class Wrap {
    /**
     * 控制台颜色值
     */
    const CONSOLE_COLOR_NORMAL = '30';
    const CONSOLE_COLOR_BLACK = '30';
    const CONSOLE_COLOR_ERROR = '31';
    const CONSOLE_COLOR_OK = '32';
    const CONSOLE_COLOR_WARNING = '33';
    const CONSOLE_COLOR_INFO = '34';

    /**
     * 返回控制台输出时高亮颜色
     *
     * @param  string $color CONSOLE_COLOR
     * @param         $fmt
     * @param null    $_
     * @internal param string $txt
     * @return string
     */
    static public function colorText($color=self::CONSOLE_COLOR_INFO, $fmt , $_ = null) {
        $args = func_get_args();
        array_shift($args);
        $txt = call_user_func_array('sprintf', $args);
        return "\033[0;31;".$color."m".$txt."\033[0m";
    }

    /**
     * 返回控制台输出时错误字符串颜色
     *
     * @param      $format
     * @param null $_
     * @internal param string $txt
     * @return string
     */
    static public function errorText($format, $_ = null) {
        return call_user_func_array(array(new self(), 'colorText'), array_merge([self::CONSOLE_COLOR_ERROR], func_get_args()));
    }

    /**
     * 返回控制台输出时错误字符串颜色
     *
     * @param  string $txt
     * @param null    $_
     * @return string
     */
    static public function infoText($txt, $_ = null){
        return call_user_func_array(array(new self(), 'colorText'), array_merge([self::CONSOLE_COLOR_INFO], func_get_args()));
    }

    /**
     * 返回控制台输出时错误字符串颜色
     * @param  string      $txt
     * @return string
     */
    static public function warningText($txt) {
        return call_user_func_array(array(new self(), 'colorText'), array_merge([self::CONSOLE_COLOR_WARNING], func_get_args()));
    }
} 