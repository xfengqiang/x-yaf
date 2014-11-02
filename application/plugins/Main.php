<?php
/**
 * @name MainPlugin
 * @desc Yaf定义了如下的6个Hook,插件之间的执行顺序是先进先Call
 * @see http://www.php.net/manual/en/class.yaf-plugin-abstract.php
 * @author frank.xu
 */
class MainPlugin extends Yaf_Plugin_Abstract {

    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {

    }

    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
    }

    public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        $http_host = $request->getServer('HTTP_HOST', '');
        $access_moduel = (strpos($http_host, 'i.')===0) ? 'Internal' : 'Index';
        if($access_moduel == 'Internal'){
            $request->setModuleName($access_moduel);
        }
        
        $module_name = $request->getModuleName();
        if($access_moduel != $module_name){
            throw new Yaf_Exception("Forbidden!!");
        }
        
    }

    public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
    }

    public function postDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
    }

    public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
    }
}
