<?php
/**
 * 
 * @package ${NAMESPACE}
 * @Autor: fengqiang <fengqiang1@staff.sina.com.cn>
 * @Date: 2015-02-11 14:07
 */
class Service_Yar_Client {
    private $_calls = [];
    const YAR_HOST = 'http://i.yar.xyaf.me';
    
    public static function fetchApiUrl($api, $ext_data=[]){
        $ext_data['api'] = $api;
        $url= self::YAR_HOST.'/api/index?'.http_build_query($ext_data);
        return $url;
    }
    
    public function call($api, $method, $args, $callback=null, $ext_data=[], $sync=true){
        if($sync){
            return $this->syncCall($api, $method, $args, $callback, $ext_data);
        }else{
            return $this->asyncCall($api, $method, $args, $callback, $ext_data);
        }     
    }
    
    public function loop($callback=null, $error_callback=null){
        try {
            $result = Yar_Concurrent_Client::loop(null, $error_callback ? $error_callback : array($this, 'errorCallback'));
        } catch (Exception $ex) {
            $metadata = array(
                'Yar_Code'    => $ex->getCode(),
                'Yar_Message' => $ex->getMessage(),
            );
            throw new Exception(json_encode($metadata), null);
        }
        return $result;
    }

    protected function syncCall($api, $method, $args, $callback=null, $ext_data=[]){
        $model = Service_Yar_ModelBase::create($api);
        $response = call_user_func_array(array($model, $method), $args);
        $this->execCallBack($callback, $response);
        return 0;
    }
    
    protected function asyncCall($api, $method, $args, $callback=null, $ext_data=[]){
        $ext_data['m'] = $method;
        $api_url = self::fetchApiUrl($api, $ext_data);
        $sequence_id =  Yar_Concurrent_Client::call($api_url, $method, $args, array($this, 'callback'));
        $this->_calls[$sequence_id] = ['id'=>$sequence_id,'api'=>$api_url, 'method'=>$method, 'args'=>$args, 'callback'=>$callback];
        return $sequence_id;
    }
    
    private  function callback($response, $call_info){
        $id  = $call_info['sequence'];
        $this->_calls[$id]['response'] = $response;
        
        foreach($this->_calls as $id => $info){
            if(!isset($info['response'])){
                break;
            }
            
            $this->execCallBack(isset($info['callback'])?$info['callback']:null, $info['response']);
            unset($this->_calls[$id]);
        }
    }

    public function errorCallback($type, $error, $callinfo) {
        throw new Exception('yaf 调用失败:'.json_encode(['type'=>$type, 'error'=>$error,'callinfo'=>$callinfo]));
    }
    
    private function execCallBack($callable, $args){
        if($callable){
            call_user_func_array($callable, [$args]);
        }
    }
    
} 