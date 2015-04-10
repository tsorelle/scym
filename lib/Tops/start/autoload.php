<?php
/**
 *
 * Set up all autoloaders
 *
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/19/2015
 * Time: 6:05 PM
 */
// Composer generated autoloader in Drupal core directory
$drupalAutoloader = require_once __DIR__ . '/../../../core/vendor/autoload.php';

$libPath = realpath(__DIR__."/../..");

// swiftfmailer autoload
require_once $libPath.'/vendor/swiftmailer/lib/swift_required.php';

// Initialize TOPs autoloader
$libPath = realpath(__DIR__."/../..");
require_once($libPath . "/Tops/src/sys/TClassPath.php");
\Tops\sys\TClassPath::Create($libPath);

// Add autoload paths for application and vendor libraries not included with Drupal
\Tops\sys\TClassPath::Add('\App','App/src');
\Tops\sys\TClassPath::Add('\Doctrine\ORM','vendor/doctrine/orm/lib/Doctrine/ORM');
\Tops\sys\TClassPath::Add('\Doctrine\DBAL','vendor/doctrine/dbal/lib/Doctrine/DBAL');
\Tops\sys\TClassPath::Add('\Symfony\Component','vendor/Symfony/Component');
\Tops\sys\TClassPath::Add('\Monolog','vendor/monolog/monolog/src/Monolog');

// for Drupal 7 only:
require_once($libPath."/Drupal/src/Drupal.php");
\Tops\sys\TClassPath::Add('\Drupal','Drupal/src');


unset($libPath);

return $drupalAutoloader;

