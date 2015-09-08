<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-09-07 22:41
 */

namespace Common\Logger;

class File extends  ILogger{
    public $log_base_dir = null;
    public $log_file = null;
    public $log_level = null;
    public $auto_flush = true;
    public $auto_flush_count = 100;
    protected $log_items = [];
    
    public function initWithConfig(array $config) {
        foreach($config as $k=>$v){
            if(property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }
    
    public function writeLog(LogItem $log){
        if($log->level < $this->log_level) {
            return ;
        }
        array_push($this->log_items, $log);
        if ($this->auto_flush && $this->auto_flush_count >0 && isset($this->log_items[$this->auto_flush_count-1])) {
            $this->flush();
        }
    }
    
    public function flush() {
        $contents = '';
        foreach($this->log_items as $log_item) {
            $class = $log_item->class ? $log_item->class : $log_item->file;
            $contents .= '['.date('Y-m-d H:i:s', $log_item->time).']['.$class.':'.$log_item->line.']['.Logger::$LOG_LEVEL_STR[$log_item->level].']'.$log_item->msg."\n";
        }
        $file = $this->log_base_dir.date("/Ymd/").$this->log_file;
        $dir = dirname($file);
        if(!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        if($contents) {
            file_put_contents($file, $contents, FILE_APPEND);
        }
        
        $this->log_items = [];
    }
    
    public function __destruct(){
        $this->flush();
    }
} 