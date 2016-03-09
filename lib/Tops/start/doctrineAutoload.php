<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/12/2016
 * Time: 8:04 AM
 */

/**
 * @var  \Composer\Autoload\ClassLoader
 */
$libPath = realpath(__DIR__."/../..");
$drupalAutoloader = require_once __DIR__ . '/../../../core/vendor/autoload.php';

// Initialize TOPs autoloader
require_once($libPath . "/Tops/src/sys/TClassPath.php");
\Tops\sys\TClassPath::Create($libPath);

// Add autoload paths for application and vendor libraries not included with Drupal
\Tops\sys\TClassPath::Add('\App','App/src');
\Tops\sys\TClassPath::Add('\Doctrine\ORM','vendor/doctrine/orm/lib/Doctrine/ORM');
\Tops\sys\TClassPath::Add('\Doctrine\DBAL','vendor/doctrine/dbal/lib/Doctrine/DBAL');

unset($libPath);
return $drupalAutoloader;

