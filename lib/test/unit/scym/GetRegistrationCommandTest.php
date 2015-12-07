<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/7/2015
 * Time: 6:21 AM
 */
namespace Tops\test\unit\scym;
require_once('RegistrationFakes.php');

use App\db\api\AttenderDto;
use App\db\api\RegistrationDto;
use App\db\scym\ScymAttender;
use App\db\scym\ScymRegistration;
use App\db\ScymRegistrationsManager;
use App\services\registration\GetRegistrationCommand;
use App\services\registration\GetRegistrationCostCommand;


class GetRegistrationCommandTest extends \PHPUnit_Framework_TestCase
{
    private $manager;
    private function getManager()
    {
        if (!isset($this->manager)) {
            $this->manager = new ScymRegistrationsManager();
        }
        return $this->manager;
    }

    public function setUp()
    {
        parent::setUp();
        \Tops\sys\TObjectContainer::Clear();
        \Tops\sys\TObjectContainer::Register('configManager', '\Tops\sys\TYmlConfigManager');
    }


    private function createTestRegistration($code) {
        $dto = RegistrationFakes::makeFakeIRegistration($code);
        $registration = new ScymRegistration();
        $registration->updateFromDataTransferObject(new RegistrationDto($dto));
        return $registration;
    }

    public function createFakeAttender($id, $name = 'Terry') {
        $dto = RegistrationFakes::makeFakeIAttender($id, $name);
        $attender = new ScymAttender();
        $attender->updateFromDataTransferObject(new AttenderDto($dto));
        return $attender;
    }

    private function addFakeAttenders(ScymRegistration $registration, $attenderCount=1) {
        $id = -1;
        for($i=0;$i<$attenderCount;$i++) {
            $id -= 1;
            $attender = $this->createFakeAttender($id,"Attender$id");
            $registration->addAttender($attender);
        }
    }

    /**
     * @param $code
     * @param int $attenderCount
     * @return ScymRegistration
     */
    private function setupRegistration($code,$attenderCount=3) {
        $registration = $this->createTestRegistration($code);
        $this->addFakeAttenders($registration,$attenderCount);
        $this->getManager()->updateEntity($registration);
        return $registration;
    }

    private function deleteRegistration($code) {
        $manager = $this->getManager();
        $reg = $manager->getRegistrationByCode($code);
        if ($reg) {
            $manager->deleteRegistration($reg);
        }
    }

    public function testGetRegistrationCommandByCode()
    {
        $code = 'UNITTEST-GETREG-1';
        $this->deleteRegistration($code);
        $registration = $this->setupRegistration($code);

        $response = $this->runCommand('code',$code);

        $this->assertNotNull($response,'No response returned.');
        $this->assertEquals(0,$response->Result,"Service failed with Result '$response->Result'");
        $this->assertNotNull($response->Value,"No response returned.");

        $registration   = isset($response->Value->registration  ) ? $response->Value->registration   : null;
        $attenderList   = isset($response->Value->attenderList  ) ? $response->Value->attenderList   : null;
        $accountSummary = isset($response->Value->accountSummary) ? $response->Value->accountSummary : null;

        $this->assertNotNull($accountSummary);
        $this->assertNotNull($registration);
        $this->assertNotNull($attenderList);

        $actual = isset($registration->registrationCode) ? $registration->registrationCode : '';
        $this->assertEquals($code,$actual,'Wrong code');
        $this->assertEquals(3,count($attenderList),'Wrong attender count');

        $actual = isset($accountSummary->balance) ? substr($accountSummary->balance,1) : '';
        $this->assertEquals(0,$actual,'Wrong balance');

        $this->deleteRegistration($code);

    }

    public function testGetRegistrationCommandById() {
        $code = 'UNITTEST-GETREG-2';
        $this->deleteRegistration($code);

        $registration = $this->setupRegistration($code);
        $id = $registration->getRegistrationId();
        $response = $this->runCommand('id',$id);

        $this->assertNotNull($response,'No response returned.');
        $this->assertEquals(0,$response->Result,"Service failed with Result '$response->Result'");
        $this->assertNotNull($response->Value,"No response returned.");

        $registration   = isset($response->Value->registration  ) ? $response->Value->registration   : null;
        $attenderList   = isset($response->Value->attenderList  ) ? $response->Value->attenderList   : null;
        $accountSummary = isset($response->Value->accountSummary) ? $response->Value->accountSummary : null;

        $this->assertNotNull($accountSummary);
        $this->assertNotNull($registration);
        $this->assertNotNull($attenderList);

        $actual = isset($registration->registrationCode) ? $registration->registrationCode : '';
        $this->assertEquals($code,$actual,'Wrong code');
        $this->assertEquals(3,count($attenderList));

        $actual = isset($registration->registrationId) ? $registration->registrationId : null;
        $this->assertNotNull($actual,"No id");
        $this->assertEquals($id,$actual,'Wrong id');

        $this->assertEquals(3,count($attenderList),"Wrong attender count");

        $actual = isset($accountSummary->balance) ? substr($accountSummary->balance,1) : '';
        $this->assertEquals(0,$actual,'Wrong balsnce');

        $this->deleteRegistration($code);
    }

    public function testGetExistingRegistration() {
        $code = 'testreg1@test.com';

        $response = $this->runCommand('code',$code);

        $this->assertNotNull($response,'No response returned.');
        $this->assertEquals(0,$response->Result,"Service failed with Result '$response->Result'");
        $this->assertNotNull($response->Value,"No response returned.");

        $registration   = isset($response->Value->registration  ) ? $response->Value->registration   : null;
        $attenderList   = isset($response->Value->attenderList  ) ? $response->Value->attenderList   : null;
        $accountSummary = isset($response->Value->accountSummary) ? $response->Value->accountSummary : null;

        $this->assertNotNull($accountSummary);
        $this->assertNotNull($registration);
        $this->assertNotNull($attenderList);

        $actual = isset($registration->registrationCode) ? $registration->registrationCode : '';
        $this->assertEquals($code,$actual,'Wrong code');
        // $this->assertEquals(3,count($attenderList));


        // $this->assertEquals(3,count($attenderList),"Wrong attender count");

        $actual = isset($accountSummary->balance) ? substr($accountSummary->balance,1) : '';
        $this->assertGreaterThan(0.00,(float)$actual,'Wrong balsnce');
    }


    private function runCommand($type,$value) {
        $command = new GetRegistrationCommand();
        $request = new \stdClass();
        $request->type = $type;
        $request->value = $value;
        $actual = $command->getTestResponse($request);
        return $actual;

    }

}
