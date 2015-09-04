<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-09-04 13:40
 */

namespace Common\Logger;

use Common\String\Wrap;

class Logger {
    static public  $LOG_LEVEL = 1;
    static public $LOG_BASE_DIR = './';
    
    const LOG_LEVEL_TRACE = 1;
    const LOG_LEVEL_DEBUG = 2;
    const LOG_LEVEL_INFO = 3;
    const LOG_LEVEL_WARN = 4;
    const LOG_LEVEL_ERROR = 5;
    
    static $levelStr = [
        self::LOG_LEVEL_TRACE=>'T',
        self::LOG_LEVEL_DEBUG=>'D',
        self::LOG_LEVEL_INFO=>'I',
        self::LOG_LEVEL_WARN=>'W',
        self::LOG_LEVEL_ERROR=>'E',
    ];
    static protected $fileCache = [];
    
    private $logFile = 'all.log';
    
    public static function init($baseDir){
        self::$LOG_BASE_DIR = $baseDir;
    }
    
    public static function clearFileCache() {
        foreach(self::$fileCache as $file) {
            fclose($file);
        }
    }
    
    public function __construct($logFile='') {
        $this->logFile = $logFile;
    }
    
    public function trace($fmt, $_v=null){
        $level = self::LOG_LEVEL_TRACE;
        if (self::$LOG_LEVEL > $level) {
            return ;
        }
        $msg = $this->getMsg($level, call_user_func_array('sprintf',  func_get_args()));
        if (self::isCli()) {
            echo $this->getConsoleMsg($level, $msg);
        }
        self::writeFileLog($level, $msg);
    }
    public function debug($fmt, $_v=null){
        $level = self::LOG_LEVEL_INFO;
        if (self::$LOG_LEVEL > $level) {
            return ;
        }
        $msg = $this->getMsg($level, call_user_func_array('sprintf',  func_get_args()));
        if (self::isCli()) {
            echo $this->getConsoleMsg($level, $msg);
        }
        self::writeFileLog($level, $msg);
    }
    public function info($fmt, $_v=null){
        $level = self::LOG_LEVEL_INFO;
        if (self::$LOG_LEVEL > $level) {
            return ;
        }
        $msg = $this->getMsg($level, call_user_func_array('sprintf',  func_get_args()));
        if (self::isCli()) {
            echo $this->getConsoleMsg($level, $msg);
        }
        self::writeFileLog($level, $msg);
    }
   
    public function warn($fmt, $_v=null) {
        $level = self::LOG_LEVEL_WARN;
        if (self::$LOG_LEVEL > $level) {
            return ;
        }
        $msg = $this->getMsg($level, call_user_func_array('sprintf',  func_get_args()));
        if (self::isCli()) {
            echo $this->getConsoleMsg($level, $msg);
        }
        self::writeFileLog($level, $msg);
    }
    public function error($fmt, $_v=null){
        $level = self::LOG_LEVEL_ERROR;
        if (self::$LOG_LEVEL > $level) {
            return ;
        }
        $msg = $this->getMsg($level, call_user_func_array('sprintf',  func_get_args()));
        if (self::isCli()) {
            echo $this->getConsoleMsg($level, $msg);
        }
        self::writeFileLog($level, $msg);
    }
    
    public function writeFileLog($level, $msg) {
        $fileName = empty($this->logFile) ? 'all.log' : $this->logFile;
        $filePath = self::$LOG_BASE_DIR.'/'.date('Ymd').'/'.$fileName;
        $dir = dirname($filePath);
        if(!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $file = isset(self::$fileCache[$filePath]) ? self::$fileCache[$filePath] : fopen($filePath, 'a'); 
        $ok = fwrite($file, $msg);
        if (!$ok) {
            $file = fopen($fileName, 'a');
            self::$fileCache[$filePath] = $file;
            fwrite($file, $msg);
        }
    }
   
    protected function getConsoleMsg($level, $format) {
        $color = Wrap::CONSOLE_COLOR_OK;
        switch($level){
            case self::LOG_LEVEL_WARN:
                $color = Wrap::CONSOLE_COLOR_WARNING;
                break;
            case self::LOG_LEVEL_ERROR:
                $color = Wrap::CONSOLE_COLOR_ERROR;
                break;
            default:
        }
        return Wrap::colorText($color, $format);
    }
    
    protected function getMsg($level, $format){
        return'['.date('Y-m-d H:i:s').']['.self::$levelStr[$level].']'.$format."\n";
    }
    
    protected function isCli() {
        return php_sapi_name()=='cli';
    }
} 