<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/24/2014
 * Time: 7:06 AM
 */

// Initialize TOPs autoloader
require_once __DIR__ . '/core/vendor/autoload.php';
$libPath = realpath(__DIR__."/lib");
require_once($libPath . "/Tops/src/sys/TClassPath.php");
\Tops\sys\TClassPath::Create($libPath);
unset($libPath);

\Tops\sys\TClassPath::Add('\App','App/src');
\Tops\sys\TClassPath::Add('\Doctrine\ORM','vendor/doctrine/orm/lib/Doctrine/ORM');
\Tops\sys\TClassPath::Add('\Doctrine\DBAL','vendor/doctrine/dbal/lib/Doctrine/DBAL');
\Tops\sys\TClassPath::Add('\scym','App/src');
\Tops\sys\TClassPath::Add('\Symfony','vendor/Symfony');
include (__DIR__."/lib/Tops/start/init.php");


$isDevMode = true;

use Tops\sys\TConfig;
use Tops\db\TEntityManagers;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use App\db;

if (!class_exists('\Doctrine\ORM\EntityManager',true)) {
    throw new \Exception("manager class not found");
}

ini_set("display_errors", "On");

// config
$em = \Tops\db\TEntityManagers::Get('application',true);
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($em);

