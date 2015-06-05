<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/20/2015
 * Time: 1:56 PM
 */

namespace App\services\mailboxes;
use Tops\services;
use Tops\services\TServiceCommand;
use Tops\sys\IMailbox;
use Tops\sys\IMailboxManager;
use Tops\sys\TPostOffice;


class UpdateMailboxCommand extends TServiceCommand {


    private function validateRequest($dto)
    {
        $result = true;
        if (!$dto) {
            $this->addErrorMessage('No service request recieved.');
            return false;
        }
        if (empty($dto->code)) {
            $this->addErrorMessage("No mailbox code assigned.");
            $result = false;
        }
        if (empty($dto->name)) {
            $this->addErrorMessage("No mailbox name assigned.");
            $result = false;
        }

        if (empty($dto->email)) {
            $this->addErrorMessage("No mailbox address assigned.");
            $result = false;
        }
        if (!(empty($dto->id) || is_numeric($dto->id))) {
            $this->addErrorMessage("Invalid mailbox id $dto->id.");
            $result = false;
        }
        return $result;
    }

    private function updateBox($dto, IMailboxManager $mgr,  IMailbox $box)
    {
        $box->setMailboxCode($dto->code);
        $box->setName($dto->name);
        $box->setEmail($dto->email);
        $box->setDescription($dto->description);
        $mgr->updateMailbox($box);
        $mgr->saveChanges();
    }


    protected function run()
    {
        $dto = $this->getRequest();
        if (!$this->validateRequest($dto)) {
            return;
        }
        $dto->description = empty($dto->description) ? '' : $dto->description;

        $mgr = TPostOffice::GetMailboxManager();
        $id = empty($dto->id) ? 0 : $dto->id;
        $state = $dto->state;
        $box = $mgr->findByCode($dto->code);
        $id = $box == null ? 0 : $box->getMailboxId();

        switch($state) {
            case 0 :  // no change
                break;
            case 1: // insert
                if (empty($id)) {
                    $box = $mgr->addMailbox($dto->code, $dto->name, $dto->email, $dto->description);
                    $this->addInfoMessage("Created mailbox " . $box->getMailboxCode());
                }
                else {
                    $this->addErrorMessage("Duplicate record found for mailbox code $dto->code");
                }
                break;
            case 2: // update
                if (empty($id) ) {
                    $this->addWarningMessage('Mailbox was not found in database.');
                }
                else {
                    $this->updateBox($dto, $mgr, $box);
                    $this->addInfoMessage("Updated mailbox " . $box->getMailboxCode());
                }
                break;
            case 3: // delete
                if (empty($id)) {
                    $this->addWarningMessage('Mailbox was not found in database.');
                }
                else {
                    $mgr->drop($id);
                    $this->addInfoMessage("Deleted mailbox " . $dto->code);
                }
                break;
        }
        $list = MailboxServices::getList($this, $mgr);
        $this->setReturnValue($list);
    }
}