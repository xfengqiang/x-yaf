<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-09-07 23:43
 */

namespace Common\Logger;

abstract class ILogger {
    public function flush() {
        
    }
    abstract public function initWithConfig(array $config);
    abstract function writeLog(LogItem $log);
}