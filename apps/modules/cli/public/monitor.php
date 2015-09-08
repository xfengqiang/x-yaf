<?php
define('MODULE_PATH', dirname(__DIR__));
define('APPLICATION_PATH', realpath(MODULE_PATH.'/../../../'));
require APPLICATION_PATH.'/apps/common/App.php';
apps\common\App::init(MODULE_PATH);
use Common\Logger\Console;
use Common\Logger\File;
use Common\Logger\Logger;
$config = \Common\Config::getAppConfig('app', ENV);

class ProcessMonitor {
    /**
     * @var ProcessMonitor
     */
    private static $instance = null;

    /**
     * @var Logger
     */
    private $logger = null;
    protected $sleep_time = 5;
    
    /**
     * @return ProcessMonitor
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new ProcessMonitor();

            
            
            $fileLogger = new File();
            $fileLogger->initWithConfig(array('log_level'=>1, 'log_file'=>'process_monitor'));
            Logger::logger()->addLogger($fileLogger);

            
            $consoleLogger = new Console();
            $consoleLogger->initWithConfig(array('log_level'=>1));
            Logger::logger()->addLogger($consoleLogger);

            self::$instance->logger = Logger::logger();
            
        }        
        return self::$instance;
    }
    
    private function __construct() {
    }
    
    public function run() {
        while(true) {
            try{
                $this->start('all');
            }catch (Exception $e) {
                $this->logger->error('===============error start==============');
                $this->logger->error($e->getMessage());
                $this->logger->error($e->getTraceAsString());
                $this->logger->error('===============error end==============');
            }
            sleep($this->sleep_time);
        }
    }
    
    //运行单个进程
    public function start($process) {
        $configs = \Common\Config::getAppConfig('crontab');
        if ($process == 'all') {
            foreach($configs as $process=>$config) {
                $this->startProcess($process, $configs[$process]);    
            }
        }else if(isset($configs[$process])){
            $this->startProcess($process, $configs[$process]);
        }else{
            $this->logger->error("Uknown process {$process}");
        }
    }


    public function stop($process) {
        $configs = \Common\Config::getAppConfig('crontab');
        if ($process == 'all') {
            foreach($configs as $process=>$config) {
                $this->stopProcess($process, $configs[$process]);
            }
        }else if(isset($configs[$process])){
            $this->stopProcess($process, $configs[$process]);
        }else{
            $this->logger->error("Uknown process {$process}");
        }
    }

    public function restart($process){
        $this->stop($process);
        $this->start($process);
    }
    
    public function status($process){
        $configs = \Common\Config::getAppConfig('crontab');
        if ($process == 'all') {
            foreach($configs as $process=>$config) {
                $this->checkStatus($process, $configs[$process]);
            }
        }else if(isset($configs[$process])){
            $this->checkStatus($process, $configs[$process]);
        }else{
            $this->logger->error("Uknown process {$process}");
        }
    }

    protected function checkStatus($name, $configs) {
        $proc_num = isset($configs['proc_num']) ? $configs['proc_num'] : 1;
        $proc_num = $proc_num<=0 ? 1 : $proc_num;
        for ($i = 1; $i<=$proc_num; $i++) {
            $cmd = $this->getStartCmd($configs['proc_uri'], $i);
            $pid = $this->getPids($cmd);
            if ($pid) {
                $this->logger->info("Process [{$name}:{$i} pid:{$pid}] running.");
            }else if($this->shouldRunProcess($name, $configs)){
                $this->logger->error("Process [{$name}:{$i} ] stopped !!!");
            }else{
                $this->logger->info("Process [{$name}:{$i} ] not running.");
            }
        }
    }
   

    /**
     * @param $cmd
     * @return string
     */
    public function getPids($cmd) {
        $checkCmd = "ps aux|grep '{$cmd}'|grep -v 'grep'| awk '{print $2}'";
        $pids = \Common\Util\Tool::execCmd($checkCmd);
        return implode(",", explode("\r", trim($pids)));
    }
    
    public function stopProcess($name,  $configs) {
        $proc_num = isset($configs['proc_num']) ? $configs['proc_num'] : 1;
        $proc_num = $proc_num<=0 ? 1 : $proc_num;
        for ($i = 1; $i<=$proc_num; $i++) {
            $cmd = $this->getStartCmd($configs['proc_uri'], $i);
            $pids = $this->getPids($cmd);
            if (!$pids) {
                $this->logger->info("Process [{$name}:{$i}] not running.");
                continue;
            }
            $pids = explode(",", $pids);
            foreach($pids as $pid) {
                $pid = trim($pid);
                $pid && \Common\Util\Tool::execCmd("kill {$pid}");
                $this->logger->info("Process [{$name}:{$i} pid:{$pid}] stopped.");
            }
        }
    }
    
    public function shouldRunProcess($name, $configs) {
        if (empty($configs['proc_uri'])) {
            $this->logger->error(" Config error. No 'proc_uri' found for process [{$name}].");
            return false;
        }

        if(isset($configs['run_time']) && !$this->timeToRun(time(), $configs['run_time'])) {
            $this->logger->info("Ignore [$name]. Not a proper time.");
            return false;
        }

        if(!$this->isAllowIp($configs)) {
            $this->logger->info("Ignore [$name]. Not allowed ip.");
            return false;
        }
        return true;
    }
    public function startProcess($name, $configs) {
        if (!$this->shouldRunProcess($name, $configs)) {
            return false;
        }
        
        $proc_num = isset($configs['proc_num']) ? $configs['proc_num'] : 1;
        $proc_num = $proc_num<=0 ? 1 : $proc_num;
        for ($i = 1; $i<=$proc_num; $i++) {
            $cmd = $this->getStartCmd($configs['proc_uri'], $i);
            if ($this->getPids($cmd)) {
                $this->logger->info("Process [{$name}:{$i}] already running.");
                continue;
            }
            \Common\Util\Tool::execCmd("nohup {$cmd} > /dev/null &");
            $this->logger->info("Process [{$name}:{$i}] start ok.");
        }
        return true;
    }
    
    public function getConfigCmd($name, $configs, $task_id){
        $configs['task_name'] = $name;
        $configs['task_id'] = $task_id;
        $taskConfig = json_encode($configs);
        return "export task_config='{$taskConfig}'";
    }
    
    public function getStartCmd($uri, $task_id=0) {
        return sprintf('php %s/index.php request_uri=%s/task_id/%s', __DIR__, $uri, $task_id);
    }


    /**
     * @param $configs
     * @return bool
     */
    public function isAllowIp($configs) {
        $allowIps = isset($configs['allow_ips']) ? json_decode($configs['allow_ips'], true): [];
        $ignoreIps = isset($configs['ignore_ips']) ? json_decode($configs['ignore_ips'], true) : [];
        if (!$allowIps && !$ignoreIps){
            return true;
        }
        
        $locIp = \Common\Util\Tool::getLocalIp();
        //黑名单优先级高于白名单
        if($ignoreIps) {
            foreach($ignoreIps as $ip) {
                if (\Common\Util\Ip::ipv4_in_range($locIp, $ip)) {
                    return false;
                }
            }
        }
        
        //设置了白名单
        if($allowIps) {
            foreach($allowIps as $ip) {
                if (\Common\Util\Ip::ipv4_in_range($locIp, $ip)) {
                    return true;  //命中白名单
                }
            }
            return false; //未命中白名单
        }
        
        return true;
    }
    
    /**
     * 检查是否到了配置的执行时间，使用crontab时间配置格式，支持到分
     *
     * @param $curr_datetime
     * @param $time_str crontab 配置，支持下面的方式：
     *         30 0-22/2 * * *  //每隔两个小时的半点执行，仅双数小时执行
     *         17,47 * * * * //每小时的第17分和47分执行
     *         * * * * * //每分钟执行一次
     *         0 * * * * //整点执行
     * @return bool
     */
    public  function timeToRun($curr_datetime, $time_str) {
        $time = explode(' ', $time_str);
        if (count($time) != 5) {
            return false;
        }

        $month  = date("n", $curr_datetime); // 没有前导0
        $day    = date("j", $curr_datetime); // 没有前导0
        $hour   = date("G", $curr_datetime);
        $minute = (int)date("i", $curr_datetime);
        $week   = date("w", $curr_datetime); // w 0~6, 0:sunday  6:saturday
        if ($this->isTimeAllow($week, $time[4], 7, 0) &&
            $this->isTimeAllow($month, $time[3], 12) &&
            $this->isTimeAllow($day, $time[2], 31, 1) &&
            $this->isTimeAllow($hour, $time[1], 24) &&
            $this->isTimeAllow($minute, $time[0], 60)
        ) {
            return true;
        }
        return false;
    }

    protected function isTimeAllow($needle, $str, $TotalCounts, $start = 0) {
        if (strpos($str, ',') !== false) {
            $weekArray = explode(',', $str);
            if (in_array($needle, $weekArray))
                return true;
            return false;
        }
        $array     = explode('/', $str);
        $end       = $start + $TotalCounts - 1;
        if (isset($array[1])) {
            if ($array[1] > $TotalCounts)
                return false;
            $tmps = explode('-', $array[0]);
            if (isset($tmps[1])) {
                if ($tmps[0] < 0 || $end < $tmps[1])
                    return false;
                $start = $tmps[0];
                $end   = $tmps[1];
            } else {
                if ($tmps[0] != '*')
                    return false;
            }
            if (0 == (($needle - $start) % $array[1]))
                return true;
            return false;
        }
        $tmps = explode('-', $array[0]);
        if (isset($tmps[1])) {
            if ($tmps[0] < 0 || $end < $tmps[1])
                return false;
            if ($needle >= $tmps[0] && $needle <= $tmps[1])
                return true;
            return false;
        } else {
            if ($tmps[0] == '*' || $tmps[0] == $needle)
                return true;
            return false;
        }
    }
    public function printHelp(){
echo "
Usage:
    php monitor.php --start all|process_name
    php monitor.php --stop all|process_name
    php monitor.php --restart all|process_name
";
        
    }
};

/**
 * php monitor.php --start all|process_name
 * php monitor.php --stop all|process_name
 * php monitor.php --restart all|process_name
 */

$mgr = ProcessMonitor::instance();

/**
 * @param $argv
 * @param $params
 * @return bool
 */
function checkParams($argv, $params) {
    switch($argv[1]) {
        case '--start':
        case '--stop':
        case '--restart':
        if(!isset($argv[2])) {
            return false;
        }
        case '--start':
        case '--run':
            break;
    }
    
    return true;
}


if(count($argv) < 2) {
    $mgr->printHelp();
}else{
    
    $params = getopt('', [
        'start:',
        'stop:',
        'restart:',
        'status:',
    ]);
    
    if (!checkParams($argv, $params)) {
        $mgr->printHelp();
        exit(-1);
    }
    
    switch($argv[1]) {
        case '--run':
            $mgr->run();
            break;
        case '--start':
            $mgr->start($params['start']);
           break;
        case '--stop':
            $mgr->stop($params['stop']);
            break;
        case '--restart':
            $mgr->restart($params['restart']);
            break;
        case '--status':
            $mgr->status(isset($params['status']) ? $params['status'] : 'all');
            break;
    }
}

apps\common\App::finish();