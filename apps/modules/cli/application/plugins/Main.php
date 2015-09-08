<?php
/**
 * @name MainPlugin
 * @desc Yaf定义了如下的6个Hook,插件之间的执行顺序是先进先Call
 * @see http://www.php.net/manual/en/class.yaf-plugin-abstract.php
 * @author frank.xu
 */
class MainPlugin extends Yaf_Plugin_Abstract {

    public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        if (!$request->isCli()){
            throw new Yaf_Exception("Forbidden!!");
        }

        ;
    }

}
