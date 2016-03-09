<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/17/2015
 * Time: 11:32 AM
 */

namespace Tops\test\unit\scym;


use App\db\ScymMeetingsManager;

class ScymMeetingsManagerTest extends \PHPUnit_Framework_TestCase
{
    private $manager;

    private function getMeetingManager()
    {
        if (!isset( $this->manager)) {
            \Tops\sys\TObjectContainer::Clear();
            \Tops\sys\TObjectContainer::Register('configManager', '\Tops\sys\TYmlConfigManager');
            $this->manager = new ScymMeetingsManager();
        }
        return $this->manager;
    }

    public function testGetMeetingsList() {
        $manager = $this->getMeetingManager();
        $actual = $manager->getMeetings();
        $this->assertNotNull($actual);
        $this->assertNotEmpty($actual);
    }

    public function testGetQuarterliesList() {
        $manager = $this->getMeetingManager();
        $actual = $manager->getQuarterliesList();
        $this->assertNotNull($actual);
        $this->assertNotEmpty($actual);
    }

    public function testGetMeeting() {
        $manager = $this->getMeetingManager();
        $actual = $manager->getMeetingById(2);
        $this->assertNotNull($actual);
    }

}
