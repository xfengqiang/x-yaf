<?php
/**
 * @name Bootstrap
 * @author frank.xu
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */

use Common\Config;

class Bootstrap extends \Yaf_Bootstrap_Abstract{
   
    public function _initConfig(Yaf_Dispatcher $dispatcher) {
        \Http\View\Tpl::setViewPath(MODULE_PATH . '/application/views');
        $env = \apps\common\App::getEnv();
        if($env == 'dev') {
            \Http\View\Tpl::setJsBase('/static/src/js/');
            \Http\View\Tpl::setCssBase('/static/src/css/');
        }else{
            \Http\View\Tpl::setJsBase('/static/dist/js/');
            \Http\View\Tpl::setCssBase('/static/dist/css/');
            
        }
    }
    
	public function _initPlugin(Yaf_Dispatcher $dispatcher) {
		//注册一个插件
		$main_plugin = new MainPlugin();
		$dispatcher->registerPlugin($main_plugin);
	}

	public function _initRoute(Yaf_Dispatcher $dispatcher) {
		//在这里注册自己的路由协议,默认使用简单路由
        $config = Config::getAppConfig('router', 'routers');
        $dispatcher->getRouter()->addConfig($config);
	}
	
}
