<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-24 13:48
 */

namespace apps\common;


!defined('APPLICATION_PATH') && define('APPLICATION_PATH', realpath(__DIR__.'/../../../'));
!defined('JSON_UNESCAPED_UNICODE') && define('JSON_UNESCAPED_UNICODE', 0);

define('CONFIG_GLOBAL', 'CONFIG_GLOBAL');
require APPLICATION_PATH.'/library/XAutoLoader.php';

class Bootstrap extends  \Yaf_Bootstrap_Abstract{
    static public function init() {
        \XAutoLoader::RegisterAutoLoader(APPLICATION_PATH);
        \Common\Config::init(APPLICATION_PATH . '/apps/config', MODULE_PATH.'/application/config');
        \Http\View\Tpl::setViewPath(MODULE_PATH . '/application/views');
    }
    
//    /**
//     * @param $env
//     * @return \Yaf_Config_Ini
//     */
    static public function getCommonConfig($env=null) {
        if (\Yaf_Registry::has(CONFIG_GLOBAL.'_'.$env)) {
            return \Yaf_Registry::get(CONFIG_GLOBAL.'_'.$env);
        }
        $config = new \Yaf_Config_Ini(dirname(__DIR__)."/config/global.ini", $env);
        \Yaf_Registry::set(CONFIG_GLOBAL.'_'.$env, $config);
        return $config;
    }

//    /**
//     * @param $env
//     * @return \Yaf_Config_Ini
//     */
//    static public function getAppConfig($env) {
//        if (\Yaf_Registry::has(CONFIG_GLOBAL.'_'.$env)) {
//            return \Yaf_Registry::get(CONFIG_GLOBAL.'_'.$env);
//        }
//        $config = new \Yaf_Config_Ini(dirname(__DIR__)."/config/global.ini", $env);
//        \Yaf_Registry::set(CONFIG_GLOBAL.'_'.$env, $config);
//        return $config;
//    }
   
} 