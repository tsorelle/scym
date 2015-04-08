<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/6/2015
 * Time: 5:43 AM
 */

class ExceptionHandlerTest extends PHPUnit_Framework_TestCase {

    /**
     * @return \Tops\sys\TExceptionHandler
     */
    private function setupTestHandler(\Monolog\Handler\TestHandler $logHandler) {
        $monologger = new \Monolog\Logger("default");
        $monologger->pushHandler($logHandler);

        $logMgr = new \Tops\sys\TLogManager();
        $logger = new \Tops\sys\TLogger($logMgr);
        $logger->setLog('default',$monologger);



        $handler = new \Tops\sys\TExceptionHandler($logger);
        $handler->addPolicy( "snafus",\Tops\sys\TException::SeverityCritical,true,'default');
        $handler->addPolicy("hickups",\Tops\sys\TException::SeverityMinor, false);
        $handler->addPolicy("errors",\Tops\sys\TException::SeverityError,false,'default');
        $handler->addPolicy("warnings",\Tops\sys\TException::SeverityMinor,false,'default');

        return $handler;
    }

    public function testTException() {

        $logHandler = new \Monolog\Handler\TestHandler();
        $handler = $this->setupTestHandler($logHandler);
        try {
            throw new \Tops\sys\TException("test error");
        }
        catch (\Exception $ex) {
            $rethrow = $handler->handleException($ex);
            $this->assertFalse($rethrow,"Expected no rethrow.");
            $this->assertTrue($logHandler->hasErrorRecords(),"Expected error logged.");
        }

    }

    public function testTriggerError() {
        $logHandler = new \Monolog\Handler\TestHandler();
        $handler = $this->setupTestHandler($logHandler);
        try {
            trigger_error("Another test error");
        }
        catch (\Exception $ex) {
            $rethrow = $handler->handleException($ex);
            $this->assertFalse($rethrow,"Expected no rethrow.");
        }
        $this->assertTrue($logHandler->hasErrorRecords(),"Expected error logged.");
        $level = $this->getLogLevel($logHandler);
        $this->assertEquals(400,$level,"Expected log level 400");

    }
    public function testWarningPolicy() {
        $logHandler = new \Monolog\Handler\TestHandler();
        $handler = $this->setupTestHandler($logHandler);
        try {
            throw new \Tops\sys\TException("Error 3");
        }
        catch (\Exception $ex) {
            $rethrow = $handler->handleException($ex,'warnings');
            $this->assertFalse($rethrow,"Expected rethrow.");
            $this->assertTrue($logHandler->hasWarningRecords(),"Expected warning");
        }
        $level = $this->getLogLevel($logHandler);
        $this->assertEquals(300,$level,"Expected log level 300");

    }

    public function testNoLoggingPolicy() {
        $logHandler = new \Monolog\Handler\TestHandler();
        $handler = $this->setupTestHandler($logHandler);
        try {
            throw new \Tops\sys\TException("Error 3");
        }
        catch (\Exception $ex) {
            $rethrow = $handler->handleException($ex,'hickups');
            $this->assertFalse($rethrow,"Expected rethrow.");
            $this->assertFalse($logHandler->hasWarningRecords(),"no records expected");
            $this->assertFalse($logHandler->hasErrorRecords(),"no records expected");
        }
        $level = $this->getLogLevel($logHandler);
        $this->assertEquals(0,$level,"No logging");

    }

    public function testCriticalErrorPolicy() {
        $logHandler = new \Monolog\Handler\TestHandler();
        $handler = $this->setupTestHandler($logHandler);
        try {
            throw new \Tops\sys\TException("Error 3");
        }
        catch (\Exception $ex) {
            $rethrow = $handler->handleException($ex,'snafus');
            $this->assertTrue($rethrow,"Expected rethrow.");
            $this->assertTrue($logHandler->hasCriticalRecords(),"Expected critical error");
        }
        $level = $this->getLogLevel($logHandler);
        $this->assertEquals(500,$level,"Expected log level 500");
    }

    private function getLogLevel(\Monolog\Handler\TestHandler $logHandler) {
        $records = $logHandler->getRecords();
        $count = sizeof($records);
        if ($count < 1) {
            return 0;
        }
        $record = $records[0];
        return $record['level'];
    }

    public function testMinorError() {
        $logHandler = new \Monolog\Handler\TestHandler();
        $handler = $this->setupTestHandler($logHandler);
        try {
            throw new \Tops\sys\TException("Error 3", \Tops\sys\TException::SeverityMinor);
        }
        catch (\Exception $ex) {
            $rethrow = $handler->handleException($ex);
            $this->assertFalse($rethrow,"Expected no rethrow.");
            $this->assertFalse($logHandler->hasErrorRecords(),"Expected no errors logged.");
        }

    }
}
