<?php
define('ENV', "dev");
define('MODULE_PATH', dirname(dirname(__FILE__)));
define('APPLICATION_PATH', realpath(MODULE_PATH.'/../../../'));
require APPLICATION_PATH.'/apps/common/App.php';

apps\common\App::init(MODULE_PATH);
$config = Common\Config::getAppConfig('app', ENV);
$app = new Yaf_Application($config);
$app->bootstrap(null)->run();

