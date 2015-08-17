<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/16/2015
 * Time: 3:12 PM
 */

namespace App\services\meetings;

use App\db\ScymDirectoryManager;
use App\db\ScymMeetingsManager;
use App\db\TScymMailboxManager;
use Tops\services;
use Tops\services\TServiceCommand;
use Tops\sys\TUser;

class InitMeetingsAppCommand extends TServiceCommand
{

    protected function run()
    {
        /*
         * Result models TypeScript interface:
         *
         *    interface IInitMeetingsResponse {
         *        meetings: scymMeeting[];
         *        quarterlies: IListItem[];
         *        canEdit: boolean;
         *   }
         *
         */
        $result = new \stdClass();
        $user = TUser::getCurrent();
        $result->canEdit = $user->isAuthorized('administer meeting directory') ? 1 : 0;
        $manager = new ScymMeetingsManager();
        $result->meetings = $this->getMeetingList($manager);
        $result->quarterlies = $manager->getQuarterliesList();

        $this->setReturnValue($result);
    }

    private function getMeetingList(ScymMeetingsManager $manager)
    {
        $result = array();
        $mailboxManager = new TScymMailboxManager();
        $meetings = $manager->getMeetings();
        foreach ($meetings as $meeting) {
            $dto = $meeting->getDataTransferObject();
            $code = $meeting->getAffiliationcode();
            $mailbox = $mailboxManager->findByCode($code);
            $dto->mailFormLink = empty($mailbox) ? '' : '/mailform?box='.$code;

            array_push($result,$dto);
        }
        return $result;
    }
}