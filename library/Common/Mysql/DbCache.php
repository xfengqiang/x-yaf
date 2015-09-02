<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-23 20:05
 */

namespace Common\Mysql;

class DbCache {
    private static $dbCache = [];
    private static $dbConfigCache = [];

    /**
     * @param       $dbName
     * @param array $config ['host'=>localhost,'port'=>3306, 'dbname'=>xxxx, 'user'=>xxxx, 'password'=>xxx]
     * @param bool  $master
     */
    public static function RegisterDb($dbName, array $config, $master=true) {
        $cacheKey = self::getCacheKey($dbName, $master);
        self::$dbConfigCache[$cacheKey] = $config;
    }
   
    /*
     * @return \PDO
     */
    public static function getDb($name, $master=true){
        $cacheKey = self::getCacheKey($name, $master);
        if (isset(self::$dbCache[$cacheKey])) {
            return self::$dbCache[$cacheKey];
        }
        $dbConfig = self::getCacheConfig($name, $master);
        $port = isset($dbConfig['port']) ? $dbConfig['port'] : 3306;
        $dsn = sprintf('mysql:host=%s;dbname=%s;port=%d', $dbConfig['host'], $dbConfig['dbname'], $port);
        $pdo = new \PDO($dsn, $dbConfig['user'], $dbConfig['password'],
            array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'utf8\'',
                \PDO::ATTR_PERSISTENT => false,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_STRINGIFY_FETCHES => false,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ));
        static::$dbCache[$cacheKey] = $pdo;
        return $pdo;
    }

    private static function getCacheKey($dbName, $master=true){
        $cacheKey = $dbName;
        if (!$master){
            $cacheKey = $dbName.'-slave';
        }
        return $cacheKey;
    }

    private static function getCacheConfig($dbName, $master=true){
        $cacheKey = self::getCacheKey($dbName, $master);
        if (!isset(self::$dbConfigCache[$cacheKey]) && !$master) {
            $cacheKey = self::getCacheKey($dbName, true);
        }
        
        if (!isset(self::$dbConfigCache[$cacheKey])) {
            throw new \Exception("Database '{$dbName}' is not registered");
        }
        
        return self::$dbConfigCache[$cacheKey];
    }

} 