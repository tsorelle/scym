<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/17/2015
 * Time: 12:44 PM
 */

namespace App\services\meetings;


use App\db\scym\ScymMailbox;
use App\db\scym\ScymMeeting;
use App\db\ScymMeetingsManager;
use App\db\TScymMailboxManager;
use Tops\services\TServiceCommand;

class UpdateMeetingCommand extends TServiceCommand
{

    public function __construct() {
        $this->addAuthorization('administer meeting directory');
    }


    protected function run()
    {
        $request = $this->getRequest();
        $manager = new ScymMeetingsManager();
        $id = $request->meetingId;
        $meeting = null;
        if ($request->editState == 1) { // editState.created
            $meeting = new ScymMeeting();
        } else {
            $meeting = $manager->getMeetingById($id);
            if (empty($meeting)) {
                $this->addErrorMessage('Meeting not found for id ' . $request->Value);
                return;
            }
        }

        $valid = $meeting->updateFromDataTransferObject($request);
        if (!$valid) {
            $this->addErrorMessage('Cannot update meeting entity due to invalid data.');
        }
        if (empty($request->quarterlyMeetingId)) {
            $meeting->setQuarterlyMeeting(null);
        } else {
            $currentQuarterly = $meeting->getQuarterlyMeeting();
            if ($currentQuarterly == null || $currentQuarterly->getQuarterlyMeetingId() != $request->quarterlyMeetingId) {
                $newQuarterly = $manager->getQuarterlyMeeting($request->quarterlyMeetingId);
                $meeting->setQuarterlyMeeting($newQuarterly);
            }
        }
        $manager->updateEntity($meeting);
        $result = $meeting->getDataTransferObject();
        $mailmanager = new TScymMailboxManager();
        $mailbox = $mailmanager->findByCode($request->affiliationCode);
        if (!empty($request->email)) {
            if ($mailbox == null) {
                $mailbox = $mailmanager->addMailbox($request->affiliationCode, $request->MeetingName, $request->email, 'Meeting contac.');
            } else {
                $mailbox->setEmail($request->email);
                $mailmanager->updateMailbox($mailbox);
            }
        }
        $result->mailFormLink = empty($mailbox) ? '' : '/mailform?box='.$request->affiliationCode;
        $this->setReturnValue($result);
    }
}