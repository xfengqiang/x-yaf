<?php
/**
 *
 * @Autor: frank
 * @Date: 2015-05-24 13:48
 */

namespace apps\common;


use Common\Logger\Logger;
use Common\Config;
use Common\Mysql\DbCache;

!defined('APPLICATION_PATH') && define('APPLICATION_PATH', realpath(__DIR__.'/../../'));
!defined('JSON_UNESCAPED_UNICODE') && define('JSON_UNESCAPED_UNICODE', 0);

define('CONFIG_GLOBAL', 'CONFIG_GLOBAL');

require APPLICATION_PATH.'/library/XAutoLoader.php';



class App  {
    static $env = null;
    static $register = array();
    static public function init($modulePath) {
        \XAutoLoader::RegisterAutoLoader(APPLICATION_PATH);
        Config::init( APPLICATION_PATH . '/apps/config', $modulePath.'/config');
        self::getEnv();
        self::initDb();
    }

    static public function initDb() {
        $env = self::getEnv();
        $dbConfigs = Config::getCommonConfig('db', $env);
        foreach($dbConfigs as $name=>$configs){
            if(isset($configs['master'])){
                foreach($configs as $type=>$config){
                    DbCache::RegisterDb($name, $config, $type=='master');
                }
            }else{
                DbCache::RegisterDb($name, $configs, true);
            }
        }
    }

    static public function finish(){
        Logger::logger()->flush();
    }

    static public function getEnv (){
        if(self::$env==''){
            $ipConfig = \Common\Config::getCommonConfig('global', 'env_ips');
            $serverIp = \Common\Util\Tool::getServerIp();
            foreach($ipConfig as $env=>$ips){
                if(strpos($ips, $serverIp)!==false){
                    self::$env = $env;
                    break;
                }
            }
            if(empty(self::$env)) {
                self::$env = 'dev';
            }
            !defined('ENV') && define('ENV', self::$env);
        }

        return self::$env;
    }
    static public function setParam($key, $v){
        self::$register[$key] = $v;
    }

    /**
     * @param $key
     * @return null|mixed
     */
    static public function getParam($key) {
        return isset(self::$register[$key]) ? self::$register[$key] : null;
    }
} 