<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/5/2015
 * Time: 9:10 AM
 */

namespace Tops\test\unit\scym;
require_once('RegistrationFakes.php');

use App\db\scym\ScymAttender;
use App\db\scym\ScymRegistration;
use App\db\ScymRegistrationsManager;
use App\services\registration\SaveRegistrationChangesCommand;


class SaveRegistrationChangesCommandTest extends \PHPUnit_Framework_TestCase
{
    private $manager;

    public function setUp()
    {
        parent::setUp();
        \Tops\sys\TObjectContainer::Clear();
        \Tops\sys\TObjectContainer::Register('configManager', '\Tops\sys\TYmlConfigManager');
    }


    private function getManager()
    {
        if (!isset($this->manager)) {
            $this->manager = new ScymRegistrationsManager();
        }
        return $this->manager;
    }

    private function setRequestRegistration($request,ScymRegistration $current)
    {
        $dto = new \stdClass();
        $dto->active = 1;
        $dto->year = '2016';
        $dto->registrationCode = $current->getRegistrationCode();
        $dto->registrationId = $current->getRegistrationId();
        $dto->statusId = $current->getStatusId();
        $dto->name = $current->getName();
        $dto->address = $current->getAddress();
        $dto->city = $current->getCity();
        $dto->phone = $current->getPhone();
        $dto->email = $current->getEmail();
        $dto->notes = $current->getNotes();
        $dto->arrivalTime = $current->getArrivalTime();
        $dto->departureTime = $current->getDepartureTime();
        $dto->financialAidAmount  = $current->getFinancialAidAmount();
        $request->registration = $dto;
    }

    private function addFakeAttenders($request, $attenderCount=1) {
        $id = -1;
        for($i=0;$i<$attenderCount;$i++) {
            $id -= 1;
            $attender = RegistrationFakes::makeFakeIAttender($id,"Attender$id");
            array_push($request->updatedAttenders,$attender);
        }
    }
    private function getFakeRequest()
    {
        $request = new \stdClass();
        $request->registration = new \stdClass();
        $request->updatedAttenders = array();
        $request->deletedAttenders = array();
        $request->donations = array();

        return $request;
    }

    private function deleteRegistration($code) {
        $manager = $this->getManager();
        $reg = $manager->getRegistrationByCode($code);
        if ($reg) {
            $manager->deleteRegistration($reg);
        }
    }

    public function testSaveNewRegistration()
    {
        $code = 'UNITTEST-SAVENEW-1';
        $this->deleteRegistration($code);

        $request = $this->getFakeRequest();
        $request->registration = RegistrationFakes::makeFakeIRegistration($code);
        // $this->addFakeAttenders($request);
        $command = new SaveRegistrationChangesCommand();
        $response = $command->runTest($request);
        $this->assertNotNull($response,"No result");
        $actual = isset($response->registration->statusId) ? $response->registration->statusId : null;
        $expected = 1;
        $this->assertEquals($expected,$actual,'Wrong status');
        $this->deleteRegistration($code);
    }

    private function runUpdateTest($code,$request) {
        $manager = $this->getManager();
        $command = new SaveRegistrationChangesCommand();
        $result = $command->runTest($request);
        $this->assertNotNull($result);
        $registration = $manager->getRegistrationByCode($code);
        return $registration;
    }

    public function testStatusHandling() {
        $code = 'UNITTEST-REGSTATUS-1';
        $this->deleteRegistration($code);

        // Create new reg expect status 1
        $request = $this->getFakeRequest();
        $request->registration = RegistrationFakes::makeFakeIRegistration($code);
        $command = new SaveRegistrationChangesCommand();
        $response = $command->runTest($request);
        $this->assertNotNull($response,"No result");
        $actual = isset($response->registration->statusId) ? $response->registration->statusId : null;
        $expected = 1;  // status is one if no attenders were added
        $this->assertEquals($expected,$actual,'Wrong status for new');

        $request->registration->registrationId = $response->registration->registrationId;
        $this->addFakeAttenders($request,2);
        $command = new SaveRegistrationChangesCommand();
        $response = $command->runTest($request);
        $this->assertNotNull($response,"No result");
        $actual = isset($response->registration->statusId) ? $response->registration->statusId : null;
        $expected = 2; // status is two if attenders were added.
        $this->assertEquals($expected,$actual,'Wrong status on update');

        $this->deleteRegistration($code);

    }

    public function testAddAttenders()
    {
        $code = 'UNITTEST-ADDATTENDER-1';
        $this->deleteRegistration($code);

        // create test reg
        $request = $this->getFakeRequest();
        $request->registration = RegistrationFakes::makeFakeIRegistration($code);
        $this->addFakeAttenders($request,1);
        $registration = $this->runUpdateTest($code,$request);
        $this->assertNotNull($registration,'Registration not created');
        $expected = 1;
        $actual = $registration->getAttenders()->count();
        $this->assertEquals($expected,$actual,'Attender not created.');

        // add two attenders
        $request->registration = new \stdClass();
        $request->registration->registrationId = $registration->getRegistrationId();
        /**
         * @var $attenders ScymAttender[]
         */
        $attenders = $registration->getAttenders()->toArray();
        $updateAttender = $request->updatedAttenders[0];
        $updateId = $attenders[0]->getAttenderId();
        $updateAttender->attenderId = $updateId;
        $expectedName = 'Update-1';
        $updateAttender->firstName = $expectedName;
        $request->updatedAttenders = array($updateAttender);
        $this->addFakeAttenders($request,2);
        $registration = $this->runUpdateTest($code,$request);
        $this->assertNotNull($registration,'Registration not returned');
        $attenders = $registration->getAttenders()->toArray();
        $expected = 3;
        $actual = count($attenders);
        $this->assertEquals($expected,$actual,'Attenders not added.');

        $updateAttender = $registration->findAttender($updateId);
        $this->assertNotNull($updateAttender,'Updated attender not found');
        $actual = $updateAttender->getFirstname();
        $this->assertEquals($expectedName,$actual,'Attender not updated.');

        // delete one attender
        $request->deletedAttenders = array($updateId);
        $request->updatedAttenders=array();
        $registration = $this->runUpdateTest($code,$request);
        $this->assertNotNull($registration,'Registration not returned');
        $attenders = $registration->getAttenders()->toArray();
        $expected = 2;
        $actual = count($attenders);
        $this->assertEquals($expected,$actual,'Attender not deleted.');

        // update registration
        // create test reg
        $request = $this->getFakeRequest();
        $this->setRequestRegistration($request,$registration);
        $expected = 'Updated Registration';
        $request->registration->name = $expected;
        $registration = $this->runUpdateTest($code,$request);
        $this->assertNotNull($registration,'Registration not returned');
        $actual = $registration->getName();
        $this->assertEquals($expected,$actual,'Registration not updated.');



        $this->deleteRegistration($code);


    }


}
