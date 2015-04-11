<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/22/2015
 * Time: 8:26 AM
 */

namespace App\services;
use Tops\services;
use Tops\services\TServiceCommand;

class TestGetServiceCommand extends TServiceCommand {

    protected function run()
    {
        $id = $this->getRequest();
        if (!$id) {
            $this->addErrorMessage('Expected request');
        }
        if ($id == 3) {
            $this->addInfoMessage("Found item");
            $item = new \stdClass();
            $item->name = "TestItem";
            $item->id = $id;
            $this->setReturnValue($item);
        }
        else {
            $this->addWarningMessage("id not found");
        }

    }
}