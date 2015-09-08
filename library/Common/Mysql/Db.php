<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-20 22:35
 */

namespace Common\Mysql;

!defined('ERR_CODE_DAO') && define('ERR_CODE_DAO', 1000);

class Db {
    private $dbName;

    /**
     * @var \PDO
     */
    private $pdo;
    
    public function __construct($name){
        $this->dbName = $name;
    }

    /**
     * @param $fields
     * @return string
     */
    private function getStrFields($fields) {
        if ( is_array($fields) ){
            $fields = '`'.implode('`,`', $fields).'`';
        }
        return $fields;
    }

    /**
     * @param $condition
     * @return array
     */
    private function getCondition($condition) {
        if (is_string($condition)) {
            return [$condition, []];
        }else if ($condition instanceof Query){
            return $condition->condition();
        }
        return $condition;
    }

    /**
     * @param        $table
     * @param        $condition
     * @param string $fields
     * @param bool   $forceMaster
     * @internal param null $params
     * @return mixed
     */
    public function fetchRow($table, $condition,  $fields='*', $forceMaster=false){
        list($condition, $params) = $this->getCondition($condition);
        $fields = $this->getStrFields($fields);
        $sql = "SELECT {$fields} FROM `{$table}`";
        
        if ( !empty($condition) ){
            $sql .= " WHERE {$condition}"; 
        }
        
        $sql .= ' LIMIT 1';
        return $this->query($sql, $params, $forceMaster)->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchRows($table, $condition, $fields='*', $forceMaster=false){
        list($condition, $params) = $this->getCondition($condition);
        $fields = $this->getStrFields($fields);
        $sql = "SELECT {$fields} FROM `{$table}`";
        if ( !empty($condition) ){
            $sql .= " WHERE {$condition}";
        }
        return $this->query($sql, $params, $forceMaster)->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function fetchVar($table, $condition,  $field, $forceMaster=false){
        list($condition, $params) = $this->getCondition($condition);
        $sql = "SELECT `{$field}` FROM `{$table}`";
        if ( !empty($condition) ){
            $sql .= " WHERE {$condition}";
        }
        return $this->query($sql, $params, $forceMaster)->fetchColumn(0);
    }
    
    public function insert($table, $data, $ignoreDuplicate=false) {
        $ignore = "";
        if ($ignoreDuplicate){
            $ignore = "IGNORE";
        }
        list($fields, $values) = Query::BuildInsertParams($data);
        $sql = sprintf("INSERT {$ignore} INTO `{$table}` SET %s", $fields);
        if ($this->query($sql, $values) === false) {
            return false;
        }
        return $this->pdo->lastInsertId();
    }

    public function batchInsert($table, $fields, $data, $ignoreDuplicate=true) {
        $strFields = $this->getStrFields($fields);
        if (is_string($fields)){
            $fields = explode(',', $fields);
        }
        $rowHolders = '('.rtrim(str_repeat('?,',count($fields)), ',').')';
        $holders = array_pad([], count($data), $rowHolders);

        $values = [];
        foreach ($data as $row){
            foreach ($fields as $field){
                array_push($values, $row[$field]);
            }
        }
        $ignore = "IGNORE";
        if (!$ignoreDuplicate){
            $ignore = "";
        }
        $sql = sprintf("INSERT {$ignore} INTO `{$table}` ({$strFields}) VALUES %s", implode(',', $holders));
        $ret = $this->query($sql, $values);
        if ($ret===false) {
            return $ret;
        }
        return $ret->rowCount();
    }
    
    public function insertOrUpdate($table, $data){
        list($fields, $values) = Query::BuildInsertParams($data);
        $sql = "INSERT INTO `{$table}` SET {$fields} ON DUPLICATE KEY UPDATE {$fields} ";
        $ret = $this->query($sql, array_merge($values, $values));
        if ($ret===false) {
            return $ret;
        }
        return $this->pdo->lastInsertId();
    }

    /**
     * @param $table
     * @param $condition
     * @param $data
     * @internal param $params
     * @return int|bool
     */
    public function update($table, $condition,  $data){
        list($condition, $params) = $this->getCondition($condition);
        list($fields, $values) = Query::BuildInsertParams($data);
        $sql = "UPDATE `{$table}` SET {$fields}";
        
        if (!empty($condition)) {
            $sql .= " WHERE {$condition}";
        }

        if ($params==null){
            $params = [];
        }
        $ret = $this->query($sql, array_merge($values, $params));
        if ($ret===false) {
            return false;
        }
        return $ret->rowCount();
    }

    /**
     * @param $table
     * @param $condition
     * @internal param $params
     * @return bool|int
     */
    public function delete($table, $condition){
        list($condition, $params) = $this->getCondition($condition);
        $sql = "DELETE FROM `{$table}`";
        if (!empty($condition)) {
            $sql .= " WHERE {$condition}";
        }
        $ret = $this->query($sql, $params);
        if ($ret===false) {
            return false;
        }
        return $ret->rowCount();
    }

    /**
     * @param      $sql
     * @param null $values
     * @param bool $forceMaster
     * @throws \Exception
     * @return false|\PDOStatement
     */
    public function query($sql, $values = null, $forceMaster=false) {
        $master = true;
        if (!$forceMaster) {
            $master = $this->isUpdate($sql);
        }

//        echo "{$sql} [isMaster:{$master}]\n";
        $this->pdo = $this->getPdo($master);
        $stat = $this->pdo->prepare($sql);
        if (!$stat) {
              throw new \Exception(sprintf("[db error] code:%s msg:%s sql:%s", $this->pdo->errorCode(), $this->pdo->errorInfo(), $sql));
        }
        
        if (!$stat->execute($values)){
            throw new \Exception(sprintf("[db error] code:%s msg:%s sql:%s", $stat->errorCode(), $stat->errorInfo(), $stat->queryString));
        }

        return $stat;
    }

    /**
     * @return bool
     */
    public function beginTransaction(){
        if ($this->pdo == null){
            $this->pdo = DbCache::getDb($this->dbName, true);    
        }
        if ($this->pdo->inTransaction()) {
            return false;
        }
        
        return $this->pdo->beginTransaction();
    }

    /**
     * @return bool
     */
    public function inTransaction() {
        return $this->pdo && $this->pdo->inTransaction();
    }

    /**
     * @return bool
     */
    public function rollback(){
        if (!$this->pdo || !$this->pdo->inTransaction()) {
            return false;
        }
        $ret = $this->pdo->rollBack();
        return $ret;
    }

    /**
     * @return bool
     */
    public function commit(){
        if (!$this->pdo || !$this->pdo->inTransaction()) {
            return false;
        }
        $ret = $this->pdo->commit();
        return $ret;
    }
    
    /**
     * @param $master
     * @return \PDO
     */
    private function getPdo($master){
        if ($this->pdo && $this->pdo->inTransaction()) {
            return $this->pdo;
        }
        return DbCache::getDb($this->dbName, $master);
    }

    /**
     * @param $sql
     * @return bool
     */
    private function isUpdate($sql){
        return strtoupper(substr(ltrim($sql), 0, 6)) != 'SELECT';
    }
} 


