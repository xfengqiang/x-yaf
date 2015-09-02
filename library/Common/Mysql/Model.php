<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-23 19:19
 */

namespace Common\Mysql;

use Common\String\Util;

interface ModelInterface {
    /**
     * @return string
     */
    public function getDb();

    /**
     * @return string
     */
    public function getTable();

    /**
     * @return array
     */
    public function getPkFields();
} 


abstract class Model implements ModelInterface{

    /**
     * @return string
     */
    public function getDb(){
        return 'default'; 
    }

    /**
     * @return string
     */
    public function getTable(){
        $class = get_class($this);
        return Util::camel2id(Util::basename($class), '_');
    }

    /**
     * @return array
     */
    public function getPkFields(){
        return ['id'];
    }
    
}