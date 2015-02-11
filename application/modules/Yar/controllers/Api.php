<?php
/**
 * @name IndexController
 * @author frank.xu
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class ApiController extends Abstract_Controller_Api {
    public $need_login = false;
    /**
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/XYaf/index/index/index/name/frank.xu 的时候, 你就会发现不同
     */
    public function indexAction() {
//        echo APPLICATION_PATH;
        $api = $this->getRequest()->getQuery('api');
        Service_Yar_Server::handle($api);
        
    }
}
