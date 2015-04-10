<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/31/2014
 * Time: 7:56 AM
 */
// require_once __DIR__ . '/../../core/vendor/autoload.php';
//include (__DIR__."/../Tops/start/init.php");
include (__DIR__."/../Tops/start/autoload.php");
\Tops\sys\TClassPath::Add('scym','App/src');


if (class_exists('Drupal\tops\Controller\TopsController')) {
    print "\nOk\n";
}