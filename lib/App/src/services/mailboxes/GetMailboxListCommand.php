<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/14/2015
 * Time: 10:59 AM
 */

namespace App\services\mailboxes;
use App\services\mailboxes\MailboxServices;
use Tops\services;
use Tops\services\TServiceCommand;
use Tops\sys\IMailboxManager;
use Tops\sys\TPostOffice;

class GetMailboxListCommand extends TServiceCommand {

    protected function run()
    {
        $mgr = TPostOffice::GetMailboxManager();
        $list = MailboxServices::getList($this,$mgr);
        $this->setReturnValue($list);
    }
}