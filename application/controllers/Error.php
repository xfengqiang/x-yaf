<?php
/**
 * @name ErrorController
 * @desc 错误控制器, 在发生未捕获的异常时刻被调用
 * @see http://www.php.net/manual/en/yaf-dispatcher.catchexception.php
 * @author frank.xu
 */
class ErrorController extends Yaf_Controller_Abstract {

	//从2.1开始, errorAction支持直接通过参数获取异常
	public function errorAction(Exception $exception) {
        $request_type = Yaf_Registry::get('request_type');
        if($request_type == 'aj'){
            $result = array('errno'=>-1, 'code'=>$exception->getCode(), 'msg'=>$exception->getMessage());
            echo Helper_String::json($result);
        }else{
            $this->getView()->assign("exception", $exception);
        }
	}
}
