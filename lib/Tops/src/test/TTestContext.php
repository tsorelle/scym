<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 3/12/2015
 * Time: 6:01 AM
 */

namespace Tops\test;


class TTestContext {
   private static $autoloader;
    public static function getAutoloader() {
        return self::$autoloader;
    }
    public static function Init($autoloader) {
        self::$autoloader = $autoloader;
    }
}