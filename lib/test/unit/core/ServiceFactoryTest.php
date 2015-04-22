<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/10/2015
 * Time: 8:04 AM
 */
use Tops\services;

class ServiceFactoryTest extends PHPUnit_Framework_TestCase {
    public function testCreateService() {
        \Tops\sys\TObjectContainer::Clear();
        \Tops\sys\TObjectContainer::Register('configManager','\Tops\sys\TYmlConfigManager');
        \Tops\sys\TObjectContainer::Register('serviceFactory','\Tops\services\TServiceFactory','configManager');

        $factory = \Tops\sys\TObjectContainer::Get('serviceFactory');

        $service = $this->createService($factory, "TestService");
        $this->assertNotNull($service,'Service not instantiated.');
        $this->assertInstanceOf('\App\services\TestServiceCommand',$service);
        $this->assertInstanceOf('\Tops\services\TServiceCommand',$service);
    }

    private function createService(Tops\Services\IServiceFactory $factory, $serviceId) {


        return $factory->CreateService($serviceId);

    }

}
