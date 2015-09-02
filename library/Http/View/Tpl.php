<?php
namespace Http\View;
/**
 * 
 * @package library/Helper/Tpl
 * @Autor: fengqiang <fengqiang1@staff.sina.com.cn>
 * @Date: 2014-11-02 13:20
 */

class Tpl {
    static private  $VIEW_PATH = '';
    static private $MODULE_NAME = '';
    
    static public function setViewPath($path){
        self::$VIEW_PATH = $path;
    }

    static public function setModuel($module){
        self::$MODULE_NAME = strtolower($module);
    }
    
    static public function getModule(){
        return self::$MODULE_NAME;
    }
    /**
     * @return string
     */
    static public function getViewPath(){
        return self::$VIEW_PATH;
    }
    
    static public function loadView($path, $_var=array()){
        extract($_var, EXTR_SKIP);
        $view_path = self::getViewPath().'/'.$path; 
        include $view_path;
    }


    /**
     * 加载CSS
     * @param string $path
     * @param boolean $return
     * @return mixed
     */
    static public function loadCss($path, $return = false) {
        if(!is_array($path)){
            $pathes = array($path);
        }else{
            $pathes = $path;
        }

        $ver = self::cssVersion();

        $result = array();
        foreach($pathes as $path){
            $href = self::getCssBaseUrl() . $path;
            $href .= (strpos($href, '?') === false ? '?' : '&') . "version={$ver}";
            $result[] = "<link href=\"{$href}\" type=\"text/css\" rel=\"stylesheet\" />";
        }
        
        if($return){
            return implode("\n", $result);
        }else{
            echo implode("\n", $result);
        }
    }

    /**
     * 加载JS
     * @param string $path
     * @param boolean $return
     * @return mixed
     */
    static public function loadJs($path, $return = false) {
        if(!is_array($path)){
            $pathes = array($path);
        }else{
            $pathes = $path;
        }

        $ver = self::jsVersion();

        $result = array();
        foreach($pathes as $path){
            $src = self::getJsBaseUrl() . $path;
            $src .= (strpos($src, '?') === false ? '?' : '&') . "version={$ver}";
            $result[] = "<script type=\"text/javascript\" src=\"{$src}\"></script>";
        }

        if ($return) {
            return implode("\n", $result);
        } else {
            echo implode("\n", $result);
        }
    }
    
    static public function getImgUrl($path) {
        $ver = self::cssVersion();
        $url = self::getCssBaseUrl() . $path;
        $url .= (strpos($url, '?') === false ? '?' : '&') . "version={$ver}";
        return $url;
    }
    
    static public function getJsBaseUrl(){
        return '/rs_'.self::getModule().'/js/';
    }
    
    static public function getCssBaseUrl(){
        return  '/rs_'.self::getModule().'/css/';
    }
    
    static public function jsVersion(){
        return "";
    }
    
    static public function cssVersion(){
        return "";
    }
} 