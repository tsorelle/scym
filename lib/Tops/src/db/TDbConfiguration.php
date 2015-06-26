<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 6/24/2015
 * Time: 4:33 AM
 */

namespace Tops\db;


use Tops\sys\IConfiguration;
use Tops\sys\TObjectContainer;
use Tops\sys\TPath;
use Tops\sys\IConfigManager;
use Tops\sys\TTracer;



class TDbConfiguration {
    private static $environment;

    /**
     * @var IConfiguration
     */
    private static $configuration;


    public static function GetEnvironment()
    {
        return self::$environment;
    }

    /**
     * @return IConfiguration
     */
    public static function GetConfiguration() {
        if (!isset(self::$configuration)) {
            /** @var IConfigManager $configManager */
            $configManager = TObjectContainer::Get('configManager');
            self::$environment = $configManager->getEnvironment();
            TTracer::Trace("Environment is ".self::$environment);

            $settings = $configManager->get('appsettings','databases');
            $connections = $configManager->get('appsettings-'.self::$environment,'connections');
            $settings->AddSection('connections',$connections);

            self::$configuration = $settings;
        }
        return self::$configuration;
    }
    public static function ReadDbConfig($typeKey="application",$isDevMode=null) {
        $config = self::GetConfiguration();
        $databaseId = $config->Value("type/$typeKey");
        if ($isDevMode === null) {
            $isDevMode = self::$environment == "development";
        }
        $connectionParams =  $config->GetSection("connections/$databaseId");
        return $connectionParams;
    }

}