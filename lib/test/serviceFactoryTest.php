<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/9/2015
 * Time: 1:34 PM
 */
require_once __DIR__ . '/../../core/vendor/autoload.php';
include (__DIR__."/../Tops/start/init.php");
\Tops\sys\TClassPath::Add('scym','App/src');
use \Symfony\Component\HttpFoundation\Request;
$req = Request::createFromGlobals();

