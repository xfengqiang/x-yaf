<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-09-04 13:40
 */

namespace Common\Logger;



class Logger {
    
    const LOG_LEVEL_TRACE = 1;
    const LOG_LEVEL_DEBUG = 2;
    const LOG_LEVEL_INFO = 3;
    const LOG_LEVEL_WARN = 4;
    const LOG_LEVEL_ERROR = 5;
    
    static $LOG_LEVEL_STR = [
        self::LOG_LEVEL_TRACE=>'T',
        self::LOG_LEVEL_DEBUG=>'D',
        self::LOG_LEVEL_INFO=>'I',
        self::LOG_LEVEL_WARN=>'W',
        self::LOG_LEVEL_ERROR=>'E',
    ];
    
    /**
     * ILogger 数组
     * @var array
     */
    private  $loggers = [];

    /**
     * @var Logger
     */
    private static $instance = null;
    
    public static function init($params=[]){
        self::logger();
        foreach($params as $logger=>$config) {
            $className = '\Common\Logger\\'.ucfirst($logger);
            if(!class_exists($className)) {
                continue;
            }
            $lg = new $className();
            $lg->initWithConfig($config);
            self::logger()->addLogger($lg);
        }
    }
    
    public  function addLogger(ILogger $logger) {
        array_push($this->loggers, $logger);
    }

    /**
     * @return Logger
     */
    public static function logger() {
        if(!self::$instance) {
            self::$instance = new Logger();
        }   
        return self::$instance;
    }
    
    private function __construct() {
        
    }
    public function trace($fmt, $_v=null){
        $this->writeLog(self::LOG_LEVEL_TRACE, call_user_func_array('sprintf',  func_get_args()));
    }
    public function debug($fmt, $_v=null){
        $this->writeLog(self::LOG_LEVEL_DEBUG, call_user_func_array('sprintf',  func_get_args()));
    }
    public function info($fmt, $_v=null){
        $this->writeLog(self::LOG_LEVEL_INFO, call_user_func_array('sprintf',  func_get_args()));
    }
   
    public function warn($fmt, $_v=null) {
        $this->writeLog(self::LOG_LEVEL_WARN, call_user_func_array('sprintf',  func_get_args()));
    }
    public function error($fmt, $_v=null){
        $this->writeLog(self::LOG_LEVEL_ERROR, call_user_func_array('sprintf',  func_get_args()));
    }
    
    protected function writeLog($level, $msg) {
        $logItem = new LogItem();
        $logItem->time = time();
        $logItem->level = $level;
        $logItem->msg = $msg;
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        $logItem->file = $trace[1]['file'];
        $logItem->line = $trace[1]['line'];
        $logItem->class = $trace[1]['class'];
        foreach($this->loggers as $logger) {
            $logger->writeLog($logItem);
        }
    }
    
    public function flush() {
        foreach($this->loggers as $logger) {
            $logger->flush();
        }
    }
} 