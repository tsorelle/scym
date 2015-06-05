<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 6/4/2015
 * Time: 6:56 AM
 */

namespace App\services\mailboxes;
use Tops\services\TServiceCommand;
use Tops\sys\IMailboxManager;

class MailboxServices {
    public static function getList(TServiceCommand $service, IMailboxManager $mgr) {
        $result = $mgr->getMailboxes();
        $count = sizeof($result);
        $list = Array();
        if ($count) {
            $service->addInfoMessage("Found $count mailboxes.");
            foreach ($result as $box) {
                $dto = new \stdClass();
                $dto->name = $box->getName();
                $dto->description = $box->getDescription();
                $dto->email = $box->getEmail();
                $dto->code = $box->getMailboxCode();
                $dto->id = $box->getMailboxId();
                $dto->state = 0;
                array_push($list,$dto);
            }
        }
        else {
            $service->addWarningMessage("No mailboxes found.");
        }
        return $list;
    }

}