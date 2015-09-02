<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-20 22:37
 */ 

class XAutoLoader {
    private static $appPath = '';
    
    public static function loadClass($class) {
        if ( substr($class, 0 , 4) == 'apps') {
            $class = str_replace('\\', '/', $class);
            $parts = explode('/', $class);
            if ($parts[1]=='common') {
                $filePath = self::$appPath.'/'.$class.'.php';
            }else{
                $moduleName = $parts[1];
                $filePath = sprintf('%s/apps/modules/%s/application/%s.php',
                    self::$appPath, $moduleName, implode('/', array_slice($parts, 2)));
            }
            require $filePath;
        }
        else {
            $filePath = self::$appPath.'/library/'.str_replace('\\', '/', $class).'.php';
            if (file_exists($filePath) ){
                require $filePath;
            }
        }
    }
    
    public static function RegisterAutoLoader($appPath){
        self::$appPath = $appPath;
        spl_autoload_register('XAutoLoader::loadClass', true, true);
    }
}
