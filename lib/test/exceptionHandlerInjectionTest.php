<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/6/2015
 * Time: 8:37 AM
 */
require_once(__DIR__.'/../Tops/start/autoload.php');
require_once(__DIR__.'/../Tops/start/init.php');
require_once(__DIR__.'/../App/start/init.php');

// use for ad hoc tests

$handler = \Tops\sys\TObjectContainer::Get('exceptionHandler');

function test(\Tops\sys\TExceptionHandler $handler, \Exception $ex) {
    $rethrow = $handler->handleException($ex);
    if ($rethrow) {
        print "RETHROW\n";
    } else {
        print "NO RETHROW\n";
    }
}

try {
    // trigger_error('test error');
    throw new \Tops\sys\TException("Test error",\Tops\sys\TException::SeverityCritical);
}
catch (\Exception $ex) {
    test($handler, $ex);
}

print "\nDone.\n";
