<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/8/2015
 * Time: 10:59 AM
 */

namespace Tops\test\unit\scym;
use App\db\api\AttenderDto;
use App\db\api\RegistrationDto;
use App\db\scym\ScymAttender;
use App\db\scym\ScymRegistration;
use App\db\ScymRegistrationsManager;
use App\services\registration\GetAttenderCommand;

require_once('RegistrationFakes.php');

class GetAttenderCommandTest  extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        \Tops\sys\TObjectContainer::Clear();
        \Tops\sys\TObjectContainer::Register('configManager', '\Tops\sys\TYmlConfigManager');
    }

    private $manager;
    private function getManager()
    {
        if (!isset($this->manager)) {
            $this->manager = new ScymRegistrationsManager();
        }
        return $this->manager;
    }

    private function deleteRegistration($code)
    {
        $manager = $this->getManager();
        $registration = $manager->getRegistrationByCode($code);
        if ($registration != null) {
            $manager->deleteRegistration($registration);
        }
    }

    private function createTestAttender($code) {
        $dto = new RegistrationDto(RegistrationFakes::makeFakeIRegistration($code));
        $registration = ScymRegistration::createNewRegistration($dto);
        $dto = new AttenderDto(RegistrationFakes::makeFakeIAttender());
        $attender = ScymAttender::CreateAttender($dto);
        $registration->addAttender($attender);
        $manager = $this->getManager();
        $manager->updateEntity($registration);
        return $attender;
    }

    public function testGetAttenderCommand() {
        $code = 'UNITTEST_GETATTENDER1';
        $this->deleteRegistration($code);
        $expectedAttender = $this->createTestAttender($code);
        $expectedId = $expectedAttender->getAttenderId();
        $expectedAttender = null;
        $response = $this->runCommand($expectedId);
        $this->assertNotNull($response,'Null response');
        $this->assertEquals(0,$response->Result,"Service failed");
        $this->assertNotNull($response->Value,"No data returned");
        $actualAttender = new AttenderDto( $response->Value->attender );
        $this->assertEquals($expectedId,$actualAttender->getAttenderId());
        $this->deleteRegistration($code);
    }

    public function testGetAttenderCommandWithLookups() {
        $code = 'UNITTEST_GETATTENDER2';
        $this->deleteRegistration($code);
        $expectedAttender = $this->createTestAttender($code);
        $expectedId = $expectedAttender->getAttenderId();
        $expectedAttender = null;
        $response = $this->runCommand($expectedId,true); // get lookups
        $this->assertNotNull($response,'Null response');
        $this->assertEquals(0,$response->Result,"Service failed");
        $this->assertNotNull($response->Value,"No data returned");
        $actualAttender = new AttenderDto( $response->Value->attender );
        $this->assertEquals($expectedId,$actualAttender->getAttenderId());
        $lookups = isset($response->Value->lookups) ? $response->Value->lookups : null;
        $this->assertNotNull($lookups,'Lookups not returned');
        // $ageGroups = isset($lookups->ageGroups) ? $lookups->ageGroups : null;
        // $this->assertNotNull($ageGroups,'Agegroups not returned');
        $affiliationCodes = isset($lookups->affiliationCodes) ? $lookups->affiliationCodes : null;
        $this->assertNotNull($affiliationCodes,'affiliationCodes not returned');
        $housingTypes = isset($lookups->housingTypes) ? $lookups->housingTypes : null;
        $this->assertNotNull($housingTypes,'housingTypes not returned');
        $this->assertNotEmpty($affiliationCodes,"affiliationCodes empty.");
        // $this->assertNotEmpty($ageGroups,"agegroups empty.");
        $this->assertNotEmpty($housingTypes,"housingTypes empty.");

        $this->deleteRegistration($code);
    }

    private function runCommand($id,$includeLookups = false) {
        $command = new GetAttenderCommand();
        $request = new \stdClass();
        $request->id = $id;
        $request->includeLookups = $includeLookups;
        $actual = $command->getTestResponse($request);
        return $actual;
    }


}