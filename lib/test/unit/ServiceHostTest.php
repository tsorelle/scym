<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/22/2015
 * Time: 6:33 AM
 */

use \Symfony\Component\HttpFoundation\Request;
use \Tops\test\TTestUser;

/**
 * Class ServiceHostUnitTest
 */
class ServiceHostTest extends PHPUnit_Framework_TestCase {
    protected function setUp()
    {
        TTestUser::addAuthorization("superuser","test authorization");
        TTestUser::addUser("superman",1,"superuser");
        TTestUser::addUser("jdoe",2,"member");

        \Tops\sys\TObjectContainer::Clear();
        \Tops\sys\TObjectContainer::Register('configManager','\Tops\sys\TYmlConfigManager');
        \Tops\sys\TObjectContainer::Register('serviceFactory','\Tops\services\TServiceFactory','configManager');
        \Tops\sys\TObjectContainer::Register('serviceHost','\Tops\services\TServiceHost','serviceFactory');



    }


    public function testExecuteService() {

        $svcRequest = new \stdClass();
        $svcRequest->testMessageText = "Testing";

        $request = new Request();
        $request->setMethod('POST');
        $request->request->set('serviceCode','TestService');
        $request->request->set( 'request', json_encode($svcRequest));

        $response = \Tops\services\TServiceHost::ExecuteRequest($request);

        $this->assertNotNull($response);
        $this->assertEquals(\Tops\services\ResultType::Success, $response->Result);
        $this->assertNotNull($response->Value);
        $this->assertEquals("Processed", $response->Value->testMessageText);
        $this->assertGreaterThan(0, sizeof( $response->Messages));
        $this->assertEquals(\Tops\services\MessageType::Info, $response->Messages[0]->MessageType);

    }

    public function testExecuteNoParameterService() {
        $svcRequest = new \stdClass();
        $svcRequest->testMessageText = "Testing";

        $request = new Request();
        $request->setMethod('POST');
        $request->request->set('serviceCode','TestService');
        // $request->request->set( 'request', json_encode($svcRequest));

        $response = \Tops\services\TServiceHost::ExecuteRequest($request);

        $this->assertNotNull($response);
        $this->assertEquals(\Tops\services\ResultType::Errors, $response->Result);
        $this->assertGreaterThan(0, sizeof( $response->Messages));
        $this->assertEquals(\Tops\services\MessageType::Error, $response->Messages[0]->MessageType);

    }
    public function testExecuteGetService() {
        $request = new Request();
        $request->setMethod('GET');
        $request->request->set('serviceCode','TestGetService');
        $request->request->set( 'request', 3);

        $response = \Tops\services\TServiceHost::ExecuteRequest($request);

        $this->assertNotNull($response);
        $this->assertEquals(\Tops\services\ResultType::Success, $response->Result);
        $this->assertNotNull($response->Value);
        $this->assertEquals("TestItem", $response->Value->name);
        $this->assertEquals(3, $response->Value->id);
        $this->assertEquals(1, sizeof( $response->Messages));
        $this->assertEquals(\Tops\services\MessageType::Info, $response->Messages[0]->MessageType);

    }

    public function testExecuteRestrictedService()
    {
        \Tops\sys\TUser::setCurrentUser(new TTestUser('superman'));

        $svcRequest = new \stdClass();
        $svcRequest->testMessageText = "Testing";

        $request = new Request();
        $request->setMethod('POST');
        $request->request->set('serviceCode','TestRestrictedService');
        // $request->request->set( 'request', json_encode($svcRequest));

        $response = \Tops\services\TServiceHost::ExecuteRequest($request);

        $this->assertNotNull($response);
        $this->assertEquals(\Tops\services\ResultType::Success, $response->Result);
        $this->assertGreaterThan(0, sizeof( $response->Messages));
        $this->assertEquals(\Tops\services\MessageType::Info, $response->Messages[0]->MessageType);



    }

    public function testExecuteUnauthorizedService()
    {
        \Tops\sys\TUser::setCurrentUser(new TTestUser('jdoe'));

        $svcRequest = new \stdClass();
        $svcRequest->testMessageText = "Testing";

        $request = new Request();
        $request->setMethod('POST');
        $request->request->set('serviceCode','TestRestrictedService');

        $response = \Tops\services\TServiceHost::ExecuteRequest($request);

        $this->assertNotNull($response);
        $this->assertEquals(\Tops\services\ResultType::Errors, $response->Result);
        $this->assertGreaterThan(0, sizeof( $response->Messages));
        $this->assertEquals(\Tops\services\MessageType::Error, $response->Messages[0]->MessageType);

    }
}
