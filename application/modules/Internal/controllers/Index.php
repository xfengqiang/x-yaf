<?php
/**
 * @name IndexController
 * @author frank.xu
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends Yaf_Controller_Abstract {

	/** 
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/XYaf/index/index/index/name/frank.xu 的时候, 你就会发现不同
     */
	public function indexAction($name = "Stranger") {
		//1. fetch query
		$get = $this->getRequest()->getQuery("get", "default value");

        echo "this is interal<br/>";        
		//2. fetch model
		$model = new SampleModel();

		//3. assign
		$this->getView()->assign("content", $model->selectSample());
		$this->getView()->assign("name", $name);
        $this->getView()->assign("title", "首页");

		//4. render by Yaf, 如果这里返回FALSE, Yaf将不会调用自动视图引擎Render模板
        return TRUE;
	}
    
    public function yarCallback($retval, $callinfo){
        var_dump($retval, $callinfo);
        return true;
    }
    public function testYarAction(){
        $dispatcher = Yaf_Dispatcher::getInstance();
        $dispatcher->autoRender(false);
        $dispatcher->disableView();
        
        Yar_Concurrent_Client::call("http://i.yar.xyaf.me/api/index", "getHotTopic", ['page'=>1,'count'=>10], array($this, 'yarCallback'));
        Yar_Concurrent_Client::loop(); //send
    }
   
}
