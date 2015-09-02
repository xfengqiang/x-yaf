<?php
/**
 * @name IndexController
 * @author frank.xu
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */


class IndexController extends \Http\Controller\Web {

	/** 
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     */
	public function indexAction($name = "Stranger") {
		//1. fetch query
		$get = $this->getRequest()->getQuery("get", "default value");
        
        
		//2. fetch model
//		$model = new SampleModel();

		//3. assign
		$this->getView()->assign("content", "this is content");
		$this->getView()->assign("name", "=======");

		//4. render by Yaf, 如果这里返回FALSE, Yaf将不会调用自动视图引擎Render模板
        return TRUE;
	}
    
    public function loginAction(){
        
    }
}
