<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/20/2015
 * Time: 1:55 PM
 */

namespace App\services\mailboxes;
use Tops\services;
use Tops\services\TServiceCommand;
use Tops\sys\TPostOffice;


class GetMailboxCommand extends TServiceCommand {

    protected function run()
    {
        $id = $this->getRequest();
        if (!$id) {
            $this->addErrorMessage('Expected mailbox id');
        }

        $mgr = TPostOffice::GetMailboxManager();
        if (is_numeric($id)) {
            $result = $mgr->find($id);
        }
        else {
            $result = $mgr->findByCode($id);
        }

        if ($result === null) {
            $this->addErrorMessage("Cannot find mailbox for id '$id'.");
        }
        else {
            $this->setReturnValue($result);
        }
    }
}