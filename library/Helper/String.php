<?php
/**
 * 
 * @package string
 * @Autor: fengqiang <fengqiang1@staff.sina.com.cn>
 * @Date: 2014-11-02 16:06
 */
class Helper_String {
    /**
     * 控制台颜色值
     */
    const CONSOLE_COLOR_NORMAL = '30';
    const CONSOLE_COLOR_BLACK = '30';
    const CONSOLE_COLOR_ERROR = '31';
    const CONSOLE_COLOR_OK = '32';
    const CONSOLE_COLOR_WARNING = '33';
    const CONSOLE_COLOR_INFO = '34';

    
    static public function json($obj){
        return json_encode($obj, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 返回控制台输出时高亮颜色
     * @param  string      $txt
     * @param  string $color CONSOLE_COLOR
     * @return string
     */
    static public function colorText($txt, $color=self::CONSOLE_COLOR_INFO) {
        if(!self::isCli() || $color==self::CONSOLE_COLOR_NORMAL)
            return $txt;
        return "\033[0;31;".$color."m".$txt."\033[0m";
    }

    /**
     * 返回控制台输出时错误字符串颜色
     * @param  string      $txt
     * @return string
     */
    static public function errorText($txt) {
        return self::colorText($txt, self::CONSOLE_COLOR_ERROR);
    }

    /**
     * 返回控制台输出时错误字符串颜色
     * @param  string      $txt
     * @return string
     */
    static public function infoText($txt) {
        return self::colorText($txt, self::CONSOLE_COLOR_INFO);
    }

    /**
     * 返回控制台输出时错误字符串颜色
     * @param  string      $txt
     * @return string
     */
    static public function warningText($txt) {
        return self::colorText($txt, self::CONSOLE_COLOR_WARNING);
    }

} 