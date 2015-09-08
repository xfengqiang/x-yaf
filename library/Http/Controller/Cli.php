<?php

/**
 * 
 * @Autor: frank
 * @Date: 2015-05-23 14:57
 */

namespace Http\Controller;

use Common\Config;
use Common\Logger\Console;
use Common\Logger\File;
use Common\Logger\Logger;

class Cli extends Base{
    
    protected $taskId; //当前task的taskId
    protected $taskName; //当前task的标识
    protected $timeLimit; //进程被运行执行的最大时长，防止进程阻塞后无法自动重启。如果任务有可能异常阻塞，可以配置此参数
    
    protected $stepCount = 100; //如果时循环处理任务，此参数用于配置每次循环处理的任务个数
    protected $sleepTime = 1; //如果是常驻任务，每次循环结束后，可以
    
    protected $configs;

    /**
     * @var \Common\Lock\Lock
     */
    protected $lock = null;
    
    /**
     * @var Logger
     */
    protected $logger;
    
    public function init() {
        parent::init();
       
        \Yaf_Dispatcher::getInstance()->autoRender(false)->disableView();
        
        $taskConfig = $this->getTaskConfig();
        $this->configs = $taskConfig;
        isset($taskConfig['task_id']) && $this->taskId = $taskConfig['task_id'];
        isset($taskConfig['step_count']) &&  $this->timeLimit = $taskConfig['step_count'];
        isset($taskConfig['sleep_time']) && $this->sleepTime = $taskConfig['sleep_time'];
        isset($taskConfig['task_name']) && $this->taskName = $taskConfig['task_name'];

        $fileLogger = new File();
        $fileLogger->log_base_dir = APPLICATION_PATH.'/logs/cli';
        $fileLogger->log_file = $this->taskName.'.'.$this->taskId.'.log';
        $fileLogger->log_level = Config::getAppConfig('app', ENV.'.logger.file.log_level', 1);
        Logger::logger()->addLogger($fileLogger);
            
        $this->logger = Logger::logger();
        
        if ($this->timeLimit) {
            set_time_limit($this->timeLimit);
        }
        
    }
    protected function getTaskConfig() {
        $r = $this->getRequest();

        $task_name = $r->getControllerName().'_'.$r->getActionName();
        $task_name = strtolower($task_name);
        $config = Config::getAppConfig('crontab', $task_name, '');

        $config['task_id'] = $r->getParam('task_id', 0);
        $config['task_name'] = $task_name;
        
        return $config;
    }
    
    public function getLock() {
        if(!$this->lock) {
            \Common\Mysql\DbCache::RegisterDb('lockdb', ['host'=>'127.0.0.1', 'port'=>3600, 'dbname'=>'xwk', 'user'=>'root', 'password'=>'z']);
            $db = new \Common\Mysql\Db('lockdb');
            $lock = new \Common\Lock\DbLock($db);
            $lock_key = $this->taskName;
        }
    }
    public function lock() {
        
    }
    
    public function unlock(){
        
    }
} 