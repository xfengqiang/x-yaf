<?php
/**
 * 
 * @package ${NAMESPACE}
 * @Autor: fengqiang <fengqiang1@staff.sina.com.cn>
 * @Date: 2015-02-11 14:07
 */
class Service_Yar_Server {
    
    public static function handle($api){
        $model = Service_Yar_Model::create($api);
        $service = new Yar_Server($model);
        $service->handle();
    }
} 