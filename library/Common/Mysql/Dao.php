<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-20 23:15
 */

namespace Common\Mysql;

class Dao {
    /**
     * @var ModelInterface
     */
    private $record;

    /**
     * @var Db
     */
    private $db = null;

    /**
     * @var Db
     */
    private $dbname = null;
    
    /**
     * @param $name
     * @return Dao
     * @throws \Exception
     */
    public static function daoByName($name) {
        $className = 'apps\common\models\\'.$name;
        if (!class_exists($className)) {
            throw new \Exception(sprintf("Class not found:%s", $className), ERR_CODE_DAO);
        }
        return new Dao(new $className());
    }
    
    public static function daoWithDb($dbname){
        $dao = new Dao();
        $dao->dbname = $dbname;
        return $dao;
    }
    
    public function __construct(ModelInterface $record=null){
        $this->record = $record;
    }
   
    public function setRecord(ModelInterface $record){
        $this->record = $record;
    }

    /**
     * @param        $params
     * @param string $fields
     * @param bool   $master
     * @return mixed
     */
    public function fetchByPk($params, $fields='*', $master=false) {
        $condition = $this->getPkCondition($params);
        return $this->getDb()->fetchRow($this->getTable(), $condition, $fields, $master);
    }

    /**
     * @param        $condition
     * @param string $fields
     * @param bool   $master
     * @internal param $params
     * @return mixed
     */
    public function fetchRow($condition, $fields='*', $master=false) {
        return $this->getDb()->fetchRow($this->getTable(), $condition, $fields, $master);
    }

    /**
     * @param        $condition
     * @param string $fields
     * @param bool   $master
     * @internal param $params
     * @return array
     */
    public function fetchRows($condition, $fields='*', $master=false) {
        return $this->getDb()->fetchRows($this->getTable(), $condition, $fields, $master);
    }

    /**
     * @param        $ids
     * @param string $fields
     * @param bool   $master
     * @return array
     * @throws \Exception
     */
    public function fetchIn($ids, $fields='*', $master=false){
        if (count($ids) == 0) {
            return [];
        }
        
        $query = new Query();
        $pkFields = $this->record->getPkFields();
        if (count($pkFields) != 1 ){
            throw new \Exception(sprintf("fetchIn only support single pk table."), ERR_CODE_DAO);
        }
        $query->addIn($pkFields[0], $ids);
        $condition = $query->condition();
        return $this->getDb()->fetchRows($this->getTable(), $condition, $fields, $master);
    }
    
    /**
     * @param $params
     * @return bool|string
     */
    public function create($params){
        return $this->getDb()->insert($this->getTable(), $params);
    }

    /**
     * @param $params
     * @return bool|int
     */
    public function updateByPk($params){
        $condition = $this->getPkCondition($params);
        return $this->update($params, $condition);
    }


    /**
     * @param $params
     * @return int|\PDOStatement
     */
    public function deleteByPk($params){
        $condition = $this->getPkCondition($params);
        return $this->delete($condition);
    }

    /**
     * @param      $data
     * @param      $condition
     * @internal param null $params
     * @return bool|int
     */
    public function update($data, $condition){
        return $this->getDb()->update($this->getTable(), $condition, $data);
    }

    /**
     * @param $condition
     * @internal param $params
     * @internal param $params
     * @return bool|int
     */
    public function delete($condition){
        return $this->getDb()->delete($this->getTable(), $condition);
    }

    public function beginTransaction(){
        if (!$this->db) {
            $this->db = new Db($this->dbname);
        }
        
        return $this->db->beginTransaction();
    }
    
    public function commit(){
        if (!$this->db) {
            return false;
        }
        
        return $this->db->commit();
    }
    
    public function rollback(){
        if (!$this->db) {
            return false;
        }

        return $this->db->rollback();
    }
    
    /**
     * @param $params
     * @return array
     * @throws \Exception
     */
    private function getPkCondition($params){
        $pkFields = $this->record->getPkFields();
        $data = [];
        foreach ($pkFields as $field) {
            if (!isset($params[$field])) {
                throw new \Exception(sprintf("Pk params '%s' is required", $field), ERR_CODE_DAO);
            }
            $data[$field] = $params[$field];
        }
        
        return Query::equalCondition($data);
    }

    /**
     * @throws \Exception
     * @return string
     */
    private function getTable(){
        if (!$this->record) {
            throw new \Exception(sprintf("record must be set for instance"), ERR_CODE_DAO);
        }
        return $this->record->getTable();
    }

    /**
     * @throws \Exception
     * @return Db
     */
    private function getDb(){
        if ($this->db) {
            return $this->db;
        }
        
        if (!$this->record) {
            throw new \Exception(sprintf("record must be set for Dao"), ERR_CODE_DAO);
        }
        
        $this->db = new Db($this->record->getDb()); 
        return $this->db;
    }
} 