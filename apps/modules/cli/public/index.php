<?php
define('ENV', "dev");
define('MODULE_PATH', dirname(dirname(__FILE__)));
define('APPLICATION_PATH', realpath(MODULE_PATH.'/../../../'));
require APPLICATION_PATH.'/apps/common/Bootstrap.php';

$config = apps\common\Bootstrap::getCommonConfig(ENV);
$app = new Yaf_Application($config->toArray(), ENV);
$app->bootstrap(null)->getDispatcher()->dispatch(new \Yaf_Request_Simple());