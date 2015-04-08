<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/31/2014
 * Time: 7:54 AM
 */
require_once __DIR__ . '/../../core/vendor/autoload.php';
include (__DIR__."/../Tops/start/init.php");
use \Symfony\Component\HttpFoundation\Request;
$req = Request::createFromGlobals();
var_dump($req);
