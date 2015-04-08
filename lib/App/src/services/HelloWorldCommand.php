<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 3/20/2015
 * Time: 10:40 AM
 */

namespace App\services;


use Tops\services\TServiceCommand;

class HelloWorldCommand extends TServiceCommand {

    protected function run()
    {
        $this->addInfoMessage('Hello world.');
    }
}