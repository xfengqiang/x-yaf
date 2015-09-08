<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-09-08 21:47
 */

namespace Common\Lock;

    /* locker table
      CREATE TABLE `locker` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `key` varchar(128) NOT NULL DEFAULT '',
      `status` tinyint(11) NOT NULL DEFAULT '0',
      `lock_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
      `unlock_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY (`id`),
      UNIQUE KEY `value` (`key`)
    ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
     */
use Common\Mysql\Query;

/**
 * Class DbLock
 * @package Common\Lock
 */
class DbLock implements Lock{
    /**
     * @var \Common\Mysql\Db
     */
    private $db = null;
    protected $lock_table = 'locker';
    
    public function __construct(\Common\Mysql\Db $db) {
        $this->db = $db;
    }
    
    public function lock($key) {
        if(!$this->db->beginTransaction()) {
            return false;
        }
        $row = $this->db->query("SELECT * FROM {$this->lock_table}  WHERE `key`=?", [$key]);
        if($row->fetch()) {
            $ret = $this->db->update($this->lock_table, "`key`='{$key}'", ['status'=>1, 'lock_time'=>(int)(microtime(true)*1000)]);
        }else{
            $ret = $this->db->insert($this->lock_table,  ['key'=>$key, 'status'=>1, 'lock_time'=>(int)(microtime(true)*1000)]);
        }
        
        return $ret;
    }
    
    public function unlock($key) {
        if(!$this->db->inTransaction()) {
            return false;
        }
        $this->db->update($this->lock_table, Query::equalCondition(['key'=>$key]), ['status'=>0, 'unlock_time'=>(int)(microtime(true)*1000)]);
        return $this->db->commit();
    }
} 