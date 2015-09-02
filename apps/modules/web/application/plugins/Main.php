<?php
/**
 * @name MainPlugin
 * @desc Yaf定义了如下的6个Hook,插件之间的执行顺序是先进先Call
 * @see http://www.php.net/manual/en/class.yaf-plugin-abstract.php
 * @author frank.xu
 */
class MainPlugin extends Yaf_Plugin_Abstract {

    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
//        session_start();
    }

    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
    }

    public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
//        echo "action:".$request->getActionName()."</br>";
//        echo "ctrl:".$request->getControllerName()."</br>";
//        echo "module:".$request->getModuleName()."</br>";
//        echo "requestUri:".$request->getRequestUri()."</br>";
//        echo $_SERVER['REQUEST_URI'];
    }

    public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
//           printf("ModuelName:%s actionName:%s controllerName:%s requestUri:%s\n",
//                    $request->getModuleName(),
//                    $request->getActionName(), $request->getControllerName(), $request->getRequestUri());
        
    }
}
