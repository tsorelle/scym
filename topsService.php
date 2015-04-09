<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/19/2015
 * Time: 6:29 AM
 */
require_once __DIR__ . '/lib/Tops/start/autoload.php';
require_once __DIR__ . '/lib/Tops/start/settings.php';
require_once __DIR__ . '/lib/Tops/start/init.php';
require_once __DIR__ . '/lib/test/temp/serviceSetup.php';

header('Content-type: application/json');
// header('Content-Type: text/html; charset=ISO-8859-1');

$response =  \Tops\services\TServiceHost::ExecuteRequest();
$testLog = \Tops\sys\TLogManager::GetTestLog();
print json_encode($response);
