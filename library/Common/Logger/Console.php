<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-09-07 22:41
 */

namespace Common\Logger;

use Common\String\Wrap;

class Console extends  ILogger{
    public $log_level;
    
    public function initWithConfig(array $config) {
        foreach($config as $k=>$v){
            if(property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }
    
    public function writeLog(LogItem $log_item){
        if($log_item->level < $this->log_level) {
            return ;
        }

        $color = Wrap::CONSOLE_COLOR_OK;
        switch($log_item->level){
            case Logger::LOG_LEVEL_WARN:
                $color = Wrap::CONSOLE_COLOR_WARNING;
                break;
            case Logger::LOG_LEVEL_ERROR:
                $color = Wrap::CONSOLE_COLOR_ERROR;
                break;
            default:
        }
        $class = $log_item->class ? $log_item->class : $log_item->file;
        $msg = '['.date('Y-m-d H:i:s', $log_item->time).']['.$class.':'.$log_item->line.']['.Logger::$LOG_LEVEL_STR[$log_item->level].']'.$log_item->msg."\n";
        echo Wrap::colorText($color, $msg);
    }
} 