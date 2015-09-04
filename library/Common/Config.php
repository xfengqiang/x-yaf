<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-27 22:40
 */

namespace Common;


class Config {
    private static $config_files = array();
    private static $app_config_path = '';
    private static $module_config_path = '';
    private static $env = 'dev';
    
    static public function init($app_config_path, $module_config_path){
        self::$app_config_path =  $app_config_path;
        self::$module_config_path = $module_config_path;
    }

    static public function setEnv($env){
        self::$env = $env;
    }
    
    static public function getEnv() {
        return self::$env;
    }
    /**
     * 读取配置信息, 找不到配置文件时，抛出异常
     *
     * @param        $file
     * @param string $key
     * @param null   $default
     * @throws \Exception
     * @return array/string    成功返回数组或string
     */
    static public function get($file, $key='', $default=null) {
//        $file = realpath($file);
        if (!isset(self::$config_files[$file])) {
            self::$config_files[$file] = new \Yaf_Config_ini($file);
        }
        
        $config_obj = self::$config_files[$file];
        if (!$key) {
            $config_value =  $config_obj ? $config_obj->toArray() : null;
        }else{
            $config_value = $config_obj->get($key);
            if (is_a($config_value, 'Yaf_Config_ini')){
                $config_value = $config_value->toArray() ;
            }
        }

        if (is_null($config_value) && $default===null) {
            throw new \Exception(1001, array('file' => $file, 'config'=>$key));
        }
        
        return $config_value===null ? $default : $config_value;
    }

    /**
     * @param        $configFile
     * @param        $key
     * @param null   $default
     * @return array
     */
    static public function getCommonConfig($configFile, $key='', $default=null){
        return self::get(self::$app_config_path.'/'.$configFile.'.ini', $key, $default);
    }

    /**
     * @param        $configFile
     * @param        $key
     * @param null   $default
     * @return array
     */
    static public function getAppConfig($configFile, $key='', $default=null){
        return self::get(self::$module_config_path.'/'.$configFile.'.ini', $key, $default);
    }
}
