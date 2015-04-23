<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/2/2015
 * Time: 7:03 AM
 */
$dbType = 'application';
$outputPath= realpath(__DIR__ . '/'. $dbType.'-'.'model');
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


use Doctrine\ORM\Tools\EntityGenerator;

ini_set("display_errors", "On");

// config
$config = new \Doctrine\ORM\Configuration();
$config->setMetadataDriverImpl($config->newDefaultAnnotationDriver($outputPath));
$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
$config->setProxyDir(__DIR__ . '/Proxies');
$config->setProxyNamespace('Proxies');
$em = \Tops\db\TEntityManagers::Get($dbType);

// custom datatypes (not mapped for reverse engineering)
$em->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('set', 'string');
$em->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

// fetch metadata
$driver = new \Doctrine\ORM\Mapping\Driver\DatabaseDriver(
    $em->getConnection()->getSchemaManager()
);

$em->getConfiguration()->setMetadataDriverImpl($driver);
$cmf = new \Doctrine\ORM\Tools\DisconnectedClassMetadataFactory();
$cmf->setEntityManager($em);	// we must set the EntityManager

$classes = $driver->getAllClassNames();
$metadata = array();
foreach ($classes as $class) {
    //any unsupported table/schema could be handled here to exclude some classes
    if (true) {
        $metadata[] = $cmf->getMetadataFor($class);
    }
}

$generator = new EntityGenerator();
$generator->setAnnotationPrefix('');   // edit: quick fix for No Metadata Classes to process
$generator->setUpdateEntityIfExists(true);	// only update if class already exists
//$generator->setRegenerateEntityIfExists(true);	// this will overwrite the existing classes
$generator->setGenerateStubMethods(true);
$generator->setGenerateAnnotations(true);
$generator->generate($metadata, $outputPath);

print 'Done!';