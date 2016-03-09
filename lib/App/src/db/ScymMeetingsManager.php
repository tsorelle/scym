<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/16/2015
 * Time: 3:29 PM
 */

namespace App\db;


use App\db\scym\ScymMeeting;
use Doctrine\ORM\EntityRepository;
use Tops\db\TDbServiceManager;
use Tops\sys\TListItem;
use App\db\scym\ScymQuarterlyMeeting;

class ScymMeetingsManager extends TDbServiceManager
{
    /**
     * @var EntityRepository
     */
    private $meetingsRepository;

    /**
     * @var EntityRepository
     */
    private $quarterliesRepository;

    /**
     * @return EntityRepository
     */
    private function getMeetingsRepository() {
        if (!isset($this->meetingsRepository)) {
            $this->meetingsRepository = $this->getRepository('App\db\scym\ScymMeeting');
        }
        return $this->meetingsRepository;
    }

    /**
     * @return EntityRepository
     */
    private function getQuarterlyMeetingsRepository() {
        if (!isset($this->quarterliesRepository)) {
            $this->quarterliesRepository = $this->getRepository('App\db\scym\ScymQuarterlyMeeting');
        }
        return $this->quarterliesRepository;
    }

    /**
     * @return \App\db\scym\ScymMeeting[]
     */
    public function getMeetings() {
        $repository = $this->getMeetingsRepository();
        $meetings = $repository->findAll();
        return $meetings;
    }

    public function getMeetingById($id) {
        $repository = $this->getMeetingsRepository();
        return $repository->find($id);
    }

    /**
     * @return ScymQuarterlyMeeting[]
     */
    private function getQuarterlies() {
        $repository = $this->getQuarterlyMeetingsRepository();
        $result = $repository->findAll();
        return $result;
    }

    /**
     * @return \stdClass[]
     */
    public function getQuarterliesList() {
        $result = array();
        $quarterlies = $this->getQuarterlies();
        foreach ($quarterlies as $q) {
            TListItem::AddToArray($result,
                $q->getQuarterlyMeetingName(),
                $q->getQuarterlyMeetingId(),
                $q->getDescription());
        }

        return $result;
    }

    public function getQuarterlyMeeting($id) {
        $repository = $this->getQuarterlyMeetingsRepository();
        $result = $repository->find($id);
        return $result;
    }

}