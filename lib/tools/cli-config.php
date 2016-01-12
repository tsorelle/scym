<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/12/2016
 * Time: 7:41 AM
 */
$dbType = 'application';
require_once(__DIR__.'/../Tops/start/autoload.php');
include (__DIR__."/../Tops/start/init.php");

\Tops\sys\TClassPath::Add('\Doctrine\ORM','vendor\doctrine\orm\lib\Doctrine\ORM');
\Tops\sys\TClassPath::Add('\Doctrine\DBAL','vendor\doctrine\dbal\lib\Doctrine\DBAL');
\Tops\sys\TClassPath::Add('\scym','App/src');

use Tops\sys\TConfig;
use Tops\db\TEntityManagers;

if (!class_exists('\Doctrine\ORM\EntityManager',true)) {
    throw new \Exception("manager class not found");
}

ini_set("display_errors", "On");

// config
$em = \Tops\db\TEntityManagers::Get($dbType,true);
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($em);
