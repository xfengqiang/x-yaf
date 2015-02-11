<?php
/**
 * 
 * @package ${NAMESPACE}
 * @Autor: fengqiang <fengqiang1@staff.sina.com.cn>
 * @Date: 2015-02-11 13:17
 */
class Yar_DemoModel extends Service_Yar_ModelBase{
    public function getHotTopic($page, $count){
        return ['data'=>[], 'total'=>0, 'page'=>$page, 'count'=>$count];
    }
    public function getFuns($uid){
        return ['uid'=>$uid];
    }
} 