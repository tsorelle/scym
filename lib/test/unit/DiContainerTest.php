<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/18/2015
 * Time: 4:49 PM
 */
use Tops\sys\TObjectContainer;


class DiContainerTest extends PHPUnit_Framework_TestCase {
    function  testContainerInstance() {
        $container = TObjectContainer::GetContainer();
        $this->assertNotNull($container,'No container');
        $this->assertInstanceOf('\Symfony\Component\DependencyInjection\ContainerBuilder',$container);
    }

    function testRegistration() {
        TObjectContainer::Register('testObject','Tops\test\TDiTestClass');
        $actual = TObjectContainer::Get('testObject');
        $this->assertNotNull($actual,'No object instantiated');
        $this->assertInstanceOf('Tops\test\TDiTestClass',$actual);
    }

    function testLoggerConstruction() {
        TObjectContainer::Clear();
        TObjectContainer::Register('errorLog','Tops\sys\TLogger');
        TObjectContainer::Register('traceLog','Tops\sys\TLogger',':trace');
        $errorLog = TObjectContainer::Get('errorLog');
        $traceLog = TObjectContainer::Get('traceLog');
        $this->assertNotNull($errorLog);
        $this->assertNotNull($traceLog);
        $this->assertEquals('default',$errorLog->getDefaultLogName());
        $this->assertEquals('trace',$traceLog->getDefaultLogName());

    }

    function testConstructorArgument() {
        TObjectContainer::Clear();
        TObjectContainer::Register('testObject','Tops\test\TDiTestClass3',':testing');
        $actual = TObjectContainer::Get('testObject');
        $this->assertNotNull($actual,'No object instantiated');
        $this->assertInstanceOf('Tops\test\TDiTestClass3',$actual);
        $value = $actual->getValue();
        $this->assertNotNull($value, 'No injected value.');
        $this->assertEquals('testing',$value);
    }

    function testConstructorInjection() {
        TObjectContainer::Clear();
        TObjectContainer::Register('argObject','Tops\test\TDiTestClass');
        $actual = TObjectContainer::Get('argObject');
        $this->assertNotNull($actual,'No object instantiated');
        $this->assertInstanceOf('Tops\test\TDiTestClass',$actual);


        TObjectContainer::Register('testObject','Tops\test\TDiTestClass2','argObject');
        $actual = TObjectContainer::Get('testObject');
        $this->assertNotNull($actual,'No object instantiated');
        $this->assertInstanceOf('Tops\test\TDiTestClass2',$actual);

        $this->assertNotNull($actual->testClass, 'No injected class.');
        $this->assertInstanceOf('Tops\test\TDiTestClass',$actual->testClass);
    }

    function testDiConfigYml() {
        TObjectContainer::Clear();

        TObjectContainer::Register('testObject2','Tops\test\TSmokeTest');
        TObjectContainer::LoadConfig("testDiConfig.yml",__DIR__);
        // TObjectContainer::LoadConfig("di.yml");
        $actual = TObjectContainer::Get('argObject');
        $this->assertNotNull($actual,'No object instantiated');
        $this->assertInstanceOf('Tops\test\TDiTestClass',$actual);


        $actual = TObjectContainer::Get('testObject');
        $this->assertNotNull($actual,'No object instantiated');
        $this->assertInstanceOf('Tops\test\TDiTestClass2',$actual);

        $this->assertNotNull($actual->testClass, 'No injected class.');
        $this->assertInstanceOf('Tops\test\TDiTestClass',$actual->testClass);

        $actual = TObjectContainer::Get('testObject2');
        $this->assertNotNull($actual,'No object instantiated');
        $this->assertInstanceOf('Tops\test\TSmokeTest',$actual);


    }

    function testConfigManager() {
        TObjectContainer::Clear();
        TObjectContainer::Register('configManager','Tops\sys\TYmlConfigManager');
        $actual = TObjectContainer::Get('configManager');
        $this->assertNotNull($actual,'No object instantiated');
        $this->assertInstanceOf('Tops\sys\TYmlConfigManager',$actual);

    }

}
