<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/14/2015
 * Time: 10:59 AM
 */

namespace App\services\mailboxes;
use Tops\services;
use Tops\services\TServiceCommand;
use Tops\sys\TPostOffice;

class GetMailboxListCommand extends TServiceCommand {

    protected function run()
    {
        $mgr = TPostOffice::GetMailboxManager();
        $result = $mgr->getMailboxes();
        $this->setReturnValue($result);
        $count = sizeof($result);
        if ($count) {
            $this->addInfoMessage("Found $count mailboxes.");
            $list = Array();
            foreach ($result as $box) {
                $dto = new \stdClass();
                $dto->name = $box->getName();
                $dto->description = $box->getDescription();
                $dto->email = $box->getEmail();
                $dto->code = $box->getMailboxCode();
                $dto->id = $box->getMailboxId();
                array_push($list,$dto);
            }
            $this->setReturnValue($list);

        } else {
            $this->addWarningMessage("No mailboxes found.");
        }

    }
}