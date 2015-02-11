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
        return self::YAR_HOST.'?api='.$api.'&'.http_build_query($ext_data);
    }
    
    public function call($api, $method, $args, $callback, $ext_data=[], $sync=true){
        if($sync){
            return $this->syncCall($api, $method, $args, $callback, $ext_data, $callback);
        }else{
            return $this->asyncCall($api, $method, $args, $callback, $ext_data, $callback);
        }     
    }

    protected function syncCall($api, $method, $args, $callback=null, $ext_data=[]){
        $model = Service_Yar_Model::create($api);
        $response = call_user_func_array(array($model, $method), $args);
        $this->execCallBack($callback, $response);
        return 0;
    }
    
    protected function asyncCall($api, $method, $args, $callback=null, $ext_data=[]){
        $ext_data['_m'] = $method;
        $api_url = self::fetchApiUrl($api, $ext_data);
        $sequence_id =  Yar_Concurrent_Client::call($api, $method, $args, array($this, 'callback'));
        $this->_calls[$sequence_id] = ['id'=>$sequence_id,'api'=>$api_url, 'method'=>$method, 'args'=>$args, 'callback'=>$callback];
        return $sequence_id;
    }
    
    private function callback($response, $call_info){
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
    
    private function execCallBack($callable, $args){
        if($callable){
            call_user_func_array($callable, $args);
        }
    }
    
} 