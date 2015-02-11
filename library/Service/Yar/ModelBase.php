<?php

/**
 * @package ${NAMESPACE}
 * @Autor   : fengqiang <fengqiang1@staff.sina.com.cn>
 * @Date    : 2015-02-11 14:24
 */
class Service_Yar_ModelBase extends Abstract_Model_Base {
    public static function create($api) {
        $class_name = 'Yar_' . $api . 'Model';
        if (!class_exists($class_name)) {
            throw new Exception('接口不存在:'.$api, -1);
        }

        return new $class_name();
    }
} 