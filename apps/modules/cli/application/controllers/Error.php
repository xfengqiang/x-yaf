<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-24 09:48
 */
class ErrorController extends \Http\Controller\Cli{
    public function errorAction(Exception $exception) {
        echo Common\String\Wrap::errorText('[%s:%s] code:%s msg:%s',
            $exception->getFile(), $exception->getLine(),
        $exception->getCode(), $exception->getMessage())."\n";
//        $result = array('code'=>$exception->getCode(), 'msg'=>$exception->getMessage());

//            var_dump($result);
//        $request_type = Yaf_Registry::get('request_type');
//        if($request_type == 'aj'){
//           
//            echo Helper_String::json($result);
//        }else{
//            $this->getView()->assign("exception", $exception);
//        }
    }
} 