<?php
define('ENV', "dev");
define('MODULE_PATH', dirname(dirname(__FILE__)));
define('APPLICATION_PATH', realpath(MODULE_PATH.'/../../../'));
require APPLICATION_PATH.'/apps/common/Bootstrap.php';

apps\common\Bootstrap::init();
$config = Common\Config::getCommon('global.'.ENV);
$app = new Yaf_Application($config);
$app->bootstrap(null)->run();

