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
use Tops\sys\TUser;


class GetMailboxCommand extends TServiceCommand {

    protected function run()
    {
        $id = $this->getRequest();
        if (!$id) {
            $this->addErrorMessage('Expected mailbox id');
        }

        $mgr = TPostOffice::GetMailboxManager();
        if (is_numeric($id)) {
            $box = $mgr->find($id);
        }
        else {
            $box = $mgr->findByCode($id);
        }

        if ($box === null) {
            $this->addErrorMessage("Cannot find mailbox for id '$id'.");
        }
        else {
            $dto = new \stdClass();
            $mailbox = new \stdClass();
            $mailbox->name = $box->getName();
            $mailbox->description = $box->getDescription();
            $mailbox->email = $box->getEmail();
            $mailbox->code = $box->getMailboxCode();
            $mailbox->id = $box->getMailboxId();
            $mailbox->state = 0;
            $dto->box = $mailbox;

            $user = TUser::getCurrent();

            $dto->fromAddress = $user->getEmail();
            if (!empty($dto->fromAddress)) {
                $dto->fromName = empty($dto->fromAddress) ? '' : $user->getFullName();
            }

            $this->setReturnValue($dto);
        }
    }
}