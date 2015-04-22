<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/26/2015
 * Time: 1:56 PM
 */
use \Tops\sys\TPostOffice;

class PostOfficeTest extends PHPUnit_Framework_TestCase {


    protected function setUp()
    {
        $sendDisabled = true;  // change to actually send the message.


        $configManager = new \Tops\sys\TYmlConfigManager();
        $mailer = new \Tops\sys\TSwiftMailer($configManager);
        $mgr = new \Tops\sys\TMemoryMailboxManager();
        $mgr->addMailbox("support","Web Support","support@testing.org","Test Mail box");
        $mgr->addMailbox("admin","Web Admin","admin@testing.org","Test Mail box");
        $mgr->addMailbox("TEST","Test box","test@testing.org","Test Mail box");
        $mgr->addMailbox("TEST2","Test box 2","test2@testing.org","Test Mail box two");
        $mgr->addMailbox("TEST3","Test box 3","test3@testing.org","Test Mail box three");

        TPostOffice::setInstance( new  TPostOffice($mailer,$mgr) );

        if (($configManager->getEnvironment() != 'development') || $sendDisabled) {
            TPostOffice::disableSend();
        }
    }

    public function testCreateMessageToUs() {
        $message = TPostOffice::CreateMessageToUs();
        $this->assertNotNull($message);
        $recipient = $message->getRecipients()[0];
        $this->assertEquals('support@testing.org',$recipient->getAddress());
    }

    public function testCreateMessageFromUs() {
        $message = TPostOffice::CreateMessageFromUs();
        $this->assertNotNull($message);
        $from = $message->getFromAddress();
        $this->assertEquals('support@testing.org',$from->getAddress());

    }

    public function testSendToUs() {
        TPostOffice::SendMessageToUs('Tester <testSendToUse@unittest.com>','Testing',"hello");
    }

    public function testSendFromUs() {
        $actual = TPostOffice::SendHtmlMessageFromUs('One <one@foo.org>; Two@foo.org','Testing','<h2>Hello</h2>');
        $this->assertEquals(2,$actual);
    }

    public function testSendMultiPartFromUs() {
        $actual = TPostOffice::SendMultiPartMessageFromUs( 'One <one@foo.org>; Two@foo.org','Testing','<h1>Testing</h1>','Plain testing');
        $this->assertEquals(2,$actual);
    }

    public function testSend() {
        $message = TPostOffice::CreateMessageFromUs();
        $message->addRecipient('test@me.com');
        $message->setSubject('testing');
        $message->setMessageBody('testing');
        $actual = TPostOffice::Send($message);
        $this->assertEquals(1,$actual);
    }



}
