<?php
define('MODULE_PATH', dirname(__DIR__));
define('APPLICATION_PATH', realpath(MODULE_PATH.'/../../../'));
require APPLICATION_PATH.'/apps/common/App.php';
apps\common\App::init(MODULE_PATH);


$config = \Common\Config::getAppConfig('app', ENV);
$app = new Yaf_Application($config, ENV);
$app->bootstrap(null)->getDispatcher()->dispatch(new \Yaf_Request_Simple());