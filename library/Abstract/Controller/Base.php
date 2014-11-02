<?php
/**
 * 
 * @package Abstract/Controller
 * @Autor: fengqiang <fengqiang1@staff.sina.com.cn>
 * @Date: 2014-11-02 13:30
 */
class Abstract_Controller_Base extends Yaf_Controller_Abstract{
    public $need_login = true;
    
    public function init(){
        $module_name = $this->getModuleName();
        if($module_name == 'Index'){
            $view_path = APPLICATION_PATH."/application/views"; 
        }else{
            $view_path = APPLICATION_PATH."/application/{$module_name}/views";
        }
        
        Helper_Tpl::setViewPath($view_path);
    }

    public function getQuery($type, $is_needed, $name, $default=""){
        $value = $this->getRequest()->getQuery($name);
        if($is_needed && !$value){
            throw new Yaf_Exception("缺少参数：".$name);
        }
        $value = $this->getValue($type, $value);
        return $value===false ? $default : $value;
    }

    public function getPost($type, $is_needed, $name, $default=""){
        $value = $this->getRequest()->getPost($name);
        if($is_needed && !$value ){
            throw new Yaf_Exception("缺少参数：".$name);
        }
        $value = $this->getValue($type, $value);
        return $value===false ? $default : $value;
    }
    
    private  function getValue($type, $value){
        switch ($type){
            case 'int':
                return intval($value);
            case 'str':
            case 'string':
                return strval($value);
        }
        
        return false;
    }


    public function getParam($name, $default=null, $method=''){
        $value = $default;
        $request = $this->getRequest();
        
        $is_get = true;
        if(!$method){
            $is_get = $request->isGet();
        }
        else{
            $is_get = ($method=='get') ? true : false;
        }
        
        if($is_get){
            $value = $this->getQuery($name);
        }else{
            $value = $this->getPost($name);
        }
        return $value;
    }

    /**
     * @param     $name
     * @param int $default
     * @return int
     */
    public function getIntParam($name, $default=0){
        $value = $this->getParam($name, $default);
        return intval($value);
    }

    /**
     * @param        $name
     * @param string $default
     * @return string
     */
    public function getStringParam($name, $default=""){
        $value = $this->getParam($name, $default);
        return strval($value);
    }
} 