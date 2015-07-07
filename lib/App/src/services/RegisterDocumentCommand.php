<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 7/7/2015
 * Time: 10:50 AM
 */

namespace App\services;


use Tops\services\TServiceCommand;
use App\sys\ScymDocumentImporter;

class RegisterDocumentCommand extends TServiceCommand {


    protected function run()
    {
        $req = $this->getRequest();
        if ($req) {
            $messages = $this->getMessages();
            $importer = new ScymDocumentImporter($messages);
            $response = new \stdClass();
            $response->nid = $importer->addDocument($req);
            $this->setReturnValue($response->nid);
        }
        else {
            $this->addErrorMessage('Expected a request');
        }

    }
}