<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/9/2015
 * Time: 1:15 PM
 */
namespace Tops\services;
use SebastianBergmann\Exporter\Exception;
use Tops\sys\IConfigManager;

/**
 * Class TServiceFactory
 * @package Tops\services
 *
 * Creates Peanut service command based on ID
 */
class TServiceFactory implements IServiceFactory {

    /**
     * Namespace for service command classes
     *
     * @var string
     */
    private $namespace;

    /**
     * @param IConfigManager $configManager
     * @throws \Exception
     */
    public function  __construct(IConfigManager $configManager = null) {
        if ($configManager !== null) {
            $appConfig = $configManager->get("appsettings");
            $namespaces = $appConfig->Value("namespaces");
            if ($namespaces == null) {
                throw new \Exception("namespaces section not found in appsettings.yml.");
            }
            $namespace = '\\' . $namespaces['root'] . '\\' . $namespaces['services'];
            $this->namespace = $namespace;
        }
    }

    /**
     * Instantiate a service factory for unit testing
     *
     * @param $namespace
     * @return TServiceFactory
     */
    public static function Create($namespace) {
        $result = new TServiceFactory();
        $result->namespace = $namespace;
        return $result;
    }

    /**
     * @inheritdoc
     */
    function CreateService($serviceId)
    {
        $className = $this->namespace.'\\'.$serviceId.'Command';
        if (!class_exists($className)) {
            throw new \Exception("Cannot instatiate service '$className'.");
        }
        return new $className();
    }
}