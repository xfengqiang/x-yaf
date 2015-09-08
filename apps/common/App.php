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
    static public function init($modulePath) {
        \XAutoLoader::RegisterAutoLoader(APPLICATION_PATH);
        Config::init( APPLICATION_PATH . '/apps/config', $modulePath.'/config', self::getEnv());
        define('ENV', self::getEnv());
        self::initDb();
    }
    
    static public function initDb() {
        $master_dbs = Config::getCommonConfig('global', 'db_master', []);
        foreach($master_dbs as $name=>$config) {
            DbCache::RegisterDb($name, $config, true);
        }
        $master_dbs = Config::getCommonConfig('global', 'db_slave', []);
        foreach($master_dbs as $name=>$config) {
            DbCache::RegisterDb($name, $config, false);
        }
    }
    static public function finish(){
        Logger::logger()->flush();
    }
    
    static public function getEnv (){
        return 'dev';
    }
} 