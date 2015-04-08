<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/24/2014
 * Time: 7:06 AM
 */
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use App\db;


require_once __DIR__ . '/vendor/autoload.php';
include (__DIR__."/lib/Tops/start/init.php");
\Tops\sys\TClassPath::Add('App','App/src');

// the connection configuration
$dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => 'scymorg',
    'password' => 'woolman',
    'dbname'   => 'scymmodel',
);

$entityPath =  array(__DIR__.  "/lib/app/src/db");
$configPath = __DIR__.  "/lib/app/src/config";
$isDevMode = true;

// Create a simple "default" Doctrine ORM configuration for Annotations
$config = Setup::createAnnotationMetadataConfiguration($entityPath, $isDevMode);
$entityManager = EntityManager::create($dbParams, $config);

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
