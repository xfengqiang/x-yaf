<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-27 22:40
 */

namespace Common;


class Config {
    private static $config_objects = array();
    private static $config_values = array();
    private static $common_config_path = '';
    private static $app_config_path = '';
    
    static public function init($config_path, $app_config_path){
        self::$common_config_path = $config_path;
        self::$app_config_path = $app_config_path;
    }

    /**
     * 读取配置信息, 找不到配置文件时，抛出异常
     *
     * @param string $path 节点路径，第一个是文件名，使用点号分隔。如:"app","product.routes"
     * @param null   $default
     * @param bool   $is_common
     * @throws \Exception
     * @return array/string    成功返回数组或string
     */
    static public function get($path, $default=null, $is_common=false) {

        if (isset(self::$config_values[$path])) {
            return self::$config_values[$path];
        }


        $arr         = explode('.', $path, 2);
        
        $prefix = $is_common ? self::$common_config_path : self::$app_config_path; 
        $config_path = $prefix .'/'.$arr[0].'.ini';

        
        if (!isset(self::$config_objects[$config_path])) {
            self::$config_objects[$config_path] = new \Yaf_Config_ini($config_path);
        }
        $config_obj = self::$config_objects[$config_path];

        $config_value = $config_obj->get(isset($arr[1]) ? $arr[1] : null );
        $config_value = is_object($config_value) ? $config_value->toArray() : $config_value;

        if (is_null($config_value) && $default===null) {
            throw new \Exception(1001, array('path' => $path));
        }
        self::$config_values[$path] = $config_value;

        return $config_value;
    }

    /**
     * @param      $path
     * @param null $default
     * @return mixed
     */
    static public function getCommon($path, $default=null){
        return self::get($path, $default, true);
    }

    /**
     * @param $path
     * @param $default
     * @return mixed
     */
    static public function getAppConfig($path, $default=null){
        return self::get($path, $default, false);
    }
}
