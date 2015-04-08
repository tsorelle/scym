<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/27/2014
 * Time: 8:45 AM
 */

namespace Tops\db;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Tops\sys\TConfig;
use Tops\sys\TPath;

class TEntityManagers {
    private static $managers;
    private static $environment;

    /**
     * Get a Doctrine entity manager based on name of database type.
     *
     * @param string $key  - name of database type
     * @return EntityManager
     * @throws \Exception
     */
    public static function Get($key='application')
    {
        self::initialize();
        if (array_key_exists($key,self::$managers))
            return self::$managers[$key];
        return self::createManager($key);
    }

    /**
     * Perform one time initialization of static class variables
     *
     * @throws \Exception
     */
    private static function initialize() {
        if (isset(self::$managers))
            return;
        self::$managers = Array();
        $env = (new TConfig("tops"))->Value("environment");
        if ($env == null) {
            throw new \Exception("No tops.yml environment setting found.");
        }
        self::$environment = $env;
    }


    /**
     * Build and initialize a Doctrine ORM entity manager based on database type key
     *
     * @param $typeKey
     * @return EntityManager
     * @throws \Doctrine\ORM\ORMException
     */
    private static function createManager($typeKey,$isDevMode=null) {
        $config = new TConfig("databases");
        $databaseId = $config->Value("type/$typeKey");
        if ($isDevMode === null) {
            $isDevMode = self::$environment == "development";
        }
        $entityPath = $config->Value("models/$databaseId");
        $metaConfigPath = TPath::FromLib($entityPath);
        $connectionParams = $config->Value(self::$environment."/connections/$databaseId");
        $metaConfig = Setup::createAnnotationMetadataConfiguration(array($metaConfigPath), $isDevMode);
        $entityManager = EntityManager::create($connectionParams,$metaConfig);
        self::$managers[$typeKey] = $entityManager;
        return $entityManager;
    }

    public static function ReadDbConfig($typeKey="application",$isDevMode=null) {
        $config = new TConfig("databases");
        $databaseId = $config->Value("type/$typeKey");
        if ($isDevMode === null) {
            $isDevMode = self::$environment == "development";
        }
        $connectionParams = $config->Value(self::$environment."/connections/$databaseId");
        return $connectionParams;
    }

}