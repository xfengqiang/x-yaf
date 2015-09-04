<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-24 13:48
 */

namespace apps\common;


use Common\Logger\Logger;

!defined('APPLICATION_PATH') && define('APPLICATION_PATH', realpath(__DIR__.'/../../'));
!defined('JSON_UNESCAPED_UNICODE') && define('JSON_UNESCAPED_UNICODE', 0);

define('CONFIG_GLOBAL', 'CONFIG_GLOBAL');

require APPLICATION_PATH.'/library/XAutoLoader.php';

class App  {
    static public function init($modulePath) {
        \XAutoLoader::RegisterAutoLoader(APPLICATION_PATH);
        \Common\Config::init( APPLICATION_PATH . '/apps/config', $modulePath.'/config', self::getEnv());
        Logger::init(APPLICATION_PATH.'/logs/'.basename($modulePath));
        define('ENV', self::getEnv());
    }
    
    static public function finish(){
        Logger::clearFileCache();
    }
    
    static public function getEnv (){
        return 'dev';
    }
} 