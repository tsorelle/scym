<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/20/2015
 * Time: 1:11 PM
 */
use \Symfony\Component\HttpFoundation\Request;
use \Tops\test\TTestUser;


class MailboxServiceTest extends PHPUnit_Framework_TestCase {
    protected function setUp()
    {
        TTestUser::addAuthorization("superuser","test authorization");
        TTestUser::addUser("superman",1,"superuser");
        TTestUser::addUser("jdoe",2,"member");

        \App\test\TestMailboxManager::setTestData();

        \Tops\sys\TObjectContainer::Clear();
        \Tops\sys\TObjectContainer::Register('userFactory','\Tops\test\TTestUserFactory');
        \Tops\sys\TObjectContainer::Register('configManager','\Tops\sys\TYmlConfigManager');
        \Tops\sys\TObjectContainer::Register('serviceFactory','\Tops\services\TServiceFactory','configManager');
        \Tops\sys\TObjectContainer::Register('serviceHost','\Tops\services\TServiceHost','serviceFactory,userFactory');
        \Tops\sys\TObjectContainer::Register('mailer','\Tops\sys\TNullMailer');
        \Tops\sys\TObjectContainer::Register('mailboxManager','\App\test\TestMailboxManager');
        \Tops\sys\TObjectContainer::Register('postoffice','\Tops\sys\TPostOffice','mailer,mailboxManager');

    }


    public function testExecuteGetMailboxListService() {
        $request = new Request();
        $request->setMethod('GET');
        $request->request->set('serviceCode','mailboxes\GetMailboxList');
        // $request->request->set( 'request', 3);

        $response = \Tops\services\TServiceHost::ExecuteRequest($request);

        $this->assertNotNull($response);
        $this->assertEquals(\Tops\services\ResultType::Success, $response->Result);
        $this->assertNotNull($response->Value);
        $boxes = $response->Value;
        $this->assertNotNull($boxes);
        $this->assertTrue(is_array($boxes));
        $this->assertEquals(2,sizeof( $boxes));
        $this->assertEquals(1, sizeof( $response->Messages));
        $this->assertEquals(\Tops\services\MessageType::Info, $response->Messages[0]->MessageType);

    }

    public function testGetMailboxService() {
        $request = new Request();
        $request->setMethod('GET');
        $request->request->set('serviceCode','mailboxes\GetMailbox');
        $request->request->set( 'request', 1);

        $response = \Tops\services\TServiceHost::ExecuteRequest($request);

        $this->assertNotNull($response);
        $this->assertEquals(\Tops\services\ResultType::Success, $response->Result);
        $this->assertNotNull($response->Value);
        $box = $response->Value;
        $this->assertEquals('Terry SoRelle',$box->getName());

    }

    public function testCreateBox() {
        $svcRequest = new \stdClass();
        $svcRequest->id = 0;
        $svcRequest->code = 'TESTCODE';
        $svcRequest->name = "Test box";
        $svcRequest->email = 'e@mail.com';

        $request = new Request();
        $request->setMethod('POST');
        $request->request->set('serviceCode','mailboxes\UpdateMailbox');
        $request->request->set( 'request', json_encode($svcRequest));

        $response = \Tops\services\TServiceHost::ExecuteRequest($request);

        $this->assertNotNull($response);
        $this->assertEquals(\Tops\services\ResultType::Success, $response->Result);
        $this->assertNotNull($response->Value);
        $box = $response->Value;
        $this->assertEquals('Test box',$box->getName());

        $request = new Request();
        $request->setMethod('GET');
        $request->request->set('serviceCode','mailboxes\GetMailbox');
        $request->request->set( 'request', $box->getMailboxId());

        $response = \Tops\services\TServiceHost::ExecuteRequest($request);

        $this->assertNotNull($response);
        $this->assertEquals(\Tops\services\ResultType::Success, $response->Result);
        $this->assertNotNull($response->Value);
        $box = $response->Value;
        $this->assertEquals('Test box',$box->getName());



    }


}
