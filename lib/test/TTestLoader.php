<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 3/16/2015
 * Time: 8:11 AM
 */

namespace Tops\test;

use Composer\Autoload\ClassLoader;

class TTestLoader {
    /**
     * @var ClassLoader
     */
    private static $loader;
    private static $modules = array();


    public static function Create(ClassLoader $loader, $modules)
    {
        self::$loader = $loader;
        self::LoadModules($modules);
    }

    public static function Load($nameSpace, $path)
    {
        self::$loader->addPsr4($nameSpace,$path);
    }
    public static function LoadModules($modules) {
        $moduleList = explode(',',$modules);
        foreach ($moduleList as $name) {
            if (!in_array($name,self::$modules)) {
                array_push(self::$modules,$name);
                self::$loader->addPsr4('Drupal\\'.$name.'\\', __DIR__.'/../../modules/'.$name.'/src');
            }
        }
    }
}