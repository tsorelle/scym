<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/28/2015
 * Time: 11:55 AM
 */
use \Tops\sys\TLogger;

class LoggerTest extends PHPUnit_Framework_TestCase {
    public function testMonologAutoload() {
        $this->assertTrue(class_exists('\Monolog\Logger'));
    }

    public function testLoggerConfig() {
        $configMgr = new \Tops\sys\TYmlConfigManager();
        $mailer = new \Tops\sys\TSwiftMailer($configMgr);
        $logMgr = new \Tops\sys\TLogManager($configMgr,$mailer);
        $errlog = $logMgr->getLog('errors');
        $this->assertNotNull($errlog);

    }

    public function testLoggerWrite() {
        $handler = new \Monolog\Handler\TestHandler();
        $monologger = new \Monolog\Logger('default');
        $monologger->pushHandler($handler);
        $logger = new TLogger( "default"); // ,$logMgr);
        $logger->setLog('default', $monologger, true);
        $logger->writeError('Test error.');
        $this->assertTrue($handler->hasErrorRecords());
    }


}
