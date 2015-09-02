<?php
/**
 * @Autor: frank
 * @Date : 2015-05-20 22:35
 */

namespace Common\Mysql;

/**
 * Class Query
 * select * from user where id = xxx
 * select * from table where col1=xxx or col2=xxx
 * select * from table where col1>1 order by a desc, c asc limit xxxx, xxx
 * select * from aa where a in (xx, xxx)
 *
 * @package Common\Mysql
 */

class Query {
    private $where = '';
    private $values = [];
    private $order = '';
    private $page = '';
    
    public static function runTest(){
        $query = new Query();
        list($sql, $values)= $query->addEqual(["id"=>1])->condition();
        printf("sql:%s values:%s\n" , $sql, json_encode($values));
        $query->reset();
        list($sql, $values) = $query->addEqual(["col1"=>1])->addOr("col2", 2)->condition();
        printf("sql:%s values:%s\n", $sql, json_encode($values));

        $query->reset();
        list($sql, $values) = $query->addCond("col1", 1, '>')->orderBy('col1', 'DESC')->orderBy('col2')->limit(1, 2)->condition();
        printf("sql:%s values:%s\n" , $sql, json_encode($values));

        $query->reset();
        list($sql, $values) = $query->addIn('id', [1,2,3])->condition();
        printf("sql:%s values:%s\n" , $sql, json_encode($values));
    }

    /**
     * @param $params
     * @return array
     */
    public static function equalCondition($params){
        $query = new Query();
        $query->addEqual($params);
        return $query->condition();
    }

    public static function query($params=[]){
        $query = new Query();
        $query->addEqual($params);
        return $query;
    }
    
    /**
     * @param $params
     * @return Query
     */
    public function addEqual($params=[]) {
        foreach ($params as $k => $v){
            $this->addCond($k, $v, '=', 'AND');
        }
        return $this;
    }

    /**
     * @param $col
     * @param $v
     * @return Query
     */
    public function addOr($col, $v){
        return $this->addCond($col, $v, '=', 'OR');
    }
    
    /**
     * @param        $col
     * @param        $v
     * @param string $oper
     * @param string $type
     * @return Query
     */
    public function addCond($col, $v, $oper='=', $type='AND'){
        if ('' != $this->where) {
            $this->where .= ' '.$type.' ';
        }
        $this->where .= "`{$col}`{$oper}?";
        array_push($this->values, $v);
        return $this;
    }

    /**
     * @param        $col
     * @param array  $values
     * @param bool   $isNot
     * @param string $type
     * @return Query
     */
    public function addIn($col, array $values, $isNot=false, $type='AND'){
        if (count($values) == 0 ){
            return $this;
        }
        
        if ('' != $this->where) {
            $this->where .= ' '.$type.' ';
        }
        $in = 'IN';
        if ($isNot) {
            $in = 'NOT IN';
        }
        $inCon = sprintf("`{$col}` {$in} (%s)", rtrim(str_repeat('?,', count($values)), ','));
        $this->where .= $inCon;
        $this->values = array_merge($this->values, $values);
        return $this;
    }

    /**
     * @param        $col
     * @param  $type
     * @return Query
     */
    public function orderBy($col, $type='ASC') {
        if ('' != $this->order){
            $this->order .= ',';
        }
        $this->order .= "`{$col}` {$type}";
        return $this;
    }

    /**
     * @param      $start
     * @param null $offset
     * @return Query
     */
    public function limit($start, $offset=null){
        if ($offset !== null) {
            $this->page = " LIMIT {$start},{$offset}";
        }else{
            $this->page = " LIMIT {$start}";
        }
        return $this;
    }

    /**
     * @return array
     */
    public function condition() {
        $where = '';
        if ($this->where != '') {
            $where = $this->where;
        }
        if ($this->order != '') {
            $where .= ' ORDER BY '.$this->order;
        }
        return [$where.$this->page, $this->values];
    }
    
    public function reset(){
        $this->where = '';
        $this->values = [];
        $this->page = '';
        $this->order = '';
    }
    
    public static function BuildInsertParams($data) {
        $holders = [];
        $values  = [];
        foreach ($data as $k => $v) {
            array_push($holders, sprintf('`%s`=?', $k));
            array_push($values, $v);
        }

        return array(join($holders, ','), $values);
    }

    public static function BuildInsertHolderParams($data) {
        $fields  = [];
        $values  = [];
        $holders = [];
        foreach ($data as $k => $v) {
            array_push($fields, sprintf('`%s`,', $k));
            array_push($holders, '?');
            array_push($values, $v);
        }

        return array(implode($fields, ','), implode($holders, ','), $values);
    }
}