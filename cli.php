<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-23 14:31
 */

define('APPLICATION_PATH', __DIR__);
$app = new Yaf_Application(APPLICATION_PATH . '/conf/application.ini');
$app->bootstrap("")->getDispatcher()->dispatch(new Yaf_Request_Simple());