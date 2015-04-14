<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 3/20/2015
 * Time: 10:40 AM
 */

namespace App\services;


use Tops\services\TServiceCommand;
use Tops\sys\TTracer;

class HelloWorldCommand extends TServiceCommand {

    protected function run()
    {
        TTracer::Trace("Starting HelloWorldCommand->run()");
        $this->addInfoMessage('Hello world.');
        TTracer::Trace("Completed HelloWorldCommand->run()");
    }
}