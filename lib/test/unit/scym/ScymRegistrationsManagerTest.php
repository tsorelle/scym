<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 10/27/2015
 * Time: 8:45 AM
 */

namespace Tops\test\unit\scym;


use App\db\api\AttenderDto;
use App\db\api\RegistrationDto;
use App\db\scym\ScymAttender;
use App\db\scym\ScymHousingAssignment;
use App\db\scym\ScymMeal;
use App\db\scym\ScymRegistration;
use App\db\scym\ScymYouth;
use App\db\ScymRegistrationsManager;

class ScymRegistrationsManagerTest extends \PHPUnit_Framework_TestCase
{
    private $manager;

    private function getManager()
    {
        if (!isset( $this->manager)) {
            \Tops\sys\TObjectContainer::Clear();
            \Tops\sys\TObjectContainer::Register('configManager', '\Tops\sys\TYmlConfigManager');
            $this->manager = new ScymRegistrationsManager();
        }
        return $this->manager;
    }

    public function testGetSessionYear() {
        $manager = $this->getManager();
        $actual = $manager->getSession();
        $this->assertNotNull($actual);
        $this->assertNotEmpty($actual);
        // $currentYear = date('Y');
        // $this->assertEquals($currentYear,$actual->getYear());
    }

    public function testGetSessionYearDTO() {
        $year = 2015;
        $manager = $this->getManager();
        $sessionYear = $manager->getSession($year);
        $this->assertNotNull($sessionYear);
        $this->assertNotEmpty($sessionYear);

        $dto = $sessionYear->toDataTransferObject();
        $this->assertNotNull($dto);
        $this->assertNotEmpty($dto);
        $expected = "April 2nd to 5th, 2015";
        $actual = $dto->datesText;
        $this->assertEquals($expected,$actual);
    }

    public function testgetUserRegistrationId() {
        $year = 2016;
        $username = 'bimbo';
        $manager = $this->getManager();
        $actual = $manager->getUserRegistrationId($username,$year);
        $this->assertNotEquals(0,$actual,'Registraion not found');
    }

    public function testGetFeeTables() {
        $manager = $this->getManager();
        $actual = $manager->getFeeTables();
        $this->assertNotNull($actual);
        $this->assertNotEmpty($actual);
    }

    public function testGetCreditTypes() {
        $manager = $this->getManager();
        $actual = $manager->getCreditTypes();
        $this->assertNotNull($actual);
        $this->assertNotEmpty($actual);
    }

    public function testHousingTypes() {
        $manager = $this->getManager();
        $actual = $manager->getHousingTypes();
        $this->assertNotNull($actual);
        $this->assertNotEmpty($actual);
    }

    public function testGetHousingTypeList() {
        $manager = $this->getManager();
        $actual = $manager->getHousingTypeList();
        $this->assertNotNull($actual);
        $this->assertNotEmpty($actual);
    }

    public function testGetFundList() {
        $manager = $this->getManager();
        $actual = $manager->getFundList();
        $this->assertNotNull($actual);
        $this->assertNotEmpty($actual);

    }

    public function testGetRegistrationAndAttenders() {
        $manager = $this->getManager();
        $regId = 178; // loflands
        $registration = $manager->getRegistration($regId);
        $this->assertNotNull($registration);
        $attenders = $registration->getAttenders();
        $attenders = $attenders->toArray();
        $actual = count($attenders);
        $expected = 4;
        $this->assertEquals($expected,$actual,'Wrong attender count');
    }

    public function testGetRegistrationCharges() {
        $manager = $this->getManager();
        $regId = 178; // loflands
        $registration = $manager->getRegistration($regId);
        $this->assertNotNull($registration);
        $charges = $registration->getCharges();
        $charges = $charges->toArray();
        $actual = count($charges);
        $expected = 6;
        $this->assertEquals($expected,$actual,'Wrong charges count');
    }

    public function testGetRegistrationCredits() {
        $manager = $this->getManager();
        $regId = 178; // loflands
        $registration = $manager->getRegistration($regId);
        $this->assertNotNull($registration);
        $credits = $registration->getCredits();
        $credits = $credits->toArray();
        $actual = count($credits);
        $expected = 1;
        $this->assertEquals($expected,$actual,'Wrong credits count');
    }

    public function testGetRegistrationDonations() {
        $manager = $this->getManager();
        $regId = 188;
        $registration = $manager->getRegistration($regId);
        $this->assertNotNull($registration);
        $donations = $registration->getDonations();
        $donations = $donations->toArray();
        $actual = count($donations);
        $expected = 2;
        $this->assertEquals($expected,$actual,'Wrong donations count');
    }

    public function testGetRegistrationPayments() {
        $manager = $this->getManager();
        $regId = 178; // loflands
        $registration = $manager->getRegistration($regId);
        $this->assertNotNull($registration);
        $payments = $registration->getPayments();
        $payments = $payments->toArray();
        $actual = count($payments);
        $expected = 1;
        $this->assertEquals($expected,$actual,'Wrong payments count');
    }

    public function testGetRegistrationMeals() {
        $manager = $this->getManager();
        $regId = 178; // loflands
        $registration = $manager->getRegistration($regId);
        $this->assertNotNull($registration);
        $attenders = $registration->getAttenders();
        $attenders = $attenders->toArray();
        $meals = $attenders[0]->getmeals();
        $meals = $meals->toArray();
        $actual = count($meals);
        $expected = 9;
        $this->assertEquals($expected,$actual,'Wrong meals count');
    }

    public function testGetRegistraionByCode() {
        $manager = $this->getManager();
        $code = 'w.wilson-reiner@sbcglobal.net';
        $registration = $manager->getRegistrationByCode($code);
        $this->assertNotNull($registration);
        $expected = 'Warren Wilson-Reiner & Bill Reiner';
        $actual = $registration->getName();
        $this->assertEquals($expected,$actual);
    }

    public function testRegistrationCodeExists() {
        $manager = $this->getManager();
        $code = 'w.wilson-reiner@sbcglobal.net';
        $result = $manager->checkRegistationCodeExists($code);
        $this->assertTrue($result,'incorrect result for existing');;
        $code = 'nobody here';
        $result = $manager->checkRegistationCodeExists($code);
        $this->assertFalse($result,'incorrect result for not existing');;

    }

    private function getFakeRegistrationDto($code)
    {
        $dto = new \stdClass();
        $dto->registrationId = -1;
        $dto->active = 1;
        $dto->year = '2016';
        $dto->registrationCode = $code;
        $dto->statusId = 1;
        $dto->name = 'Unit Test One';
        $dto->address = 'address';
        $dto->city = 'city';
        $dto->phone = 'phone';
        $dto->email = 'e@mail.com';
        $dto->notes = 'notes';
        $dto->financialAidAmount  = 0.00;
        return new RegistrationDto($dto);
    }

    private function getFakeAttender()
    {
        $attender = new \stdClass(); // fake
        $attender->attenderId = null; // 1;
        $attender->firstName = 'Terry';
        $attender->lastName = 'SoRelle';
        $attender->middleName = '';
        $attender->dateOfBirth = '';
        $attender->affiliationCode = 'FMA';
        $attender->otherAffiliation = '';
        $attender->firstTimer = 0;
        $attender->notes = '';
        $attender->linens = 0;
        $attender->arrivalTime = 41;
        $attender->departureTime = 72;
        $attender->vegetarian = 1;
        $attender->attended = 0;
        $attender->singleOccupant = 0;
        $attender->glutenFree = 0;
        $attender->housingTypeId = 2;
        $attender->specialNeedsTypeId = null;
        $attender->generationId = 1; // lookup: generations
        $attender->gradeLevel = null; // 'PS';'K'; 1 .. 13
        // $attender->ageGroupId = null; // lookup agegroups
        $attender->creditTypeId = 0; // number; // formerly: feeCredit; lookup: creditTypes
        $attender->meals = array(
            51,
            52,
            53,
            61,
            62,
            63,
            71
        );
        return $attender;
    }


    public function testCreateResistration() {
        $manager = $this->getManager();
        $code = 'UNITTESTONE';
        $reg = $this->getFakeRegistrationDto($code);
        $registration = ScymRegistration::createNewRegistration($reg);
        $this->assertNotNull($registration);
        $manager->updateEntity($registration);
        $registration = $manager->getRegistrationByCode($code);
        $this->assertNotNull($registration);
        $manager->deleteRegistration($registration);
        $registration = $manager->getRegistrationByCode($code);
        $this->assertNull($registration);
    }

    public function testCreateResistrationWithAttender() {
        $manager = $this->getManager();
        $code = 'UNITTESTWO';
        $this->deleteRegistration($code);
        $reg = $this->getFakeRegistrationDto($code);
        $registration = ScymRegistration::createNewRegistration($reg);
        $this->assertNotNull($registration);
        $fakeAttender = new AttenderDto($this->getFakeAttender());
        $attenderList = array($fakeAttender);

        $registration->addAttenders($attenderList);

        $manager->updateEntity($registration);


        $registration = $manager->getRegistrationByCode($code);
        $this->assertNotNull($registration);

        $attenders = $registration->getAttenders()->toArray();
        $expected = 1;
        $actual = count($attenders);
        $this->assertEquals($expected,$actual);

        /**
         * @var $attender ScymAttender
         */
        $attender = $attenders[0];
        $arrival = $attender->getArrivalTime();
        $this->assertNotNull($arrival);

        $meals = $attender->getMeals();
        $fakeMeals = $fakeAttender->getMeals();
        $expected = count($fakeMeals);
        $actual = count($meals);
        $this->assertEquals($expected,$actual,'meal count wrong');

        $moreMeals = array(43,72,51,52, 53);
        $removedMeals = $manager->updateMeals($attender,$moreMeals);
        $manager->updateEntity($registration);

       // $registration = $manager->getRegistrationByCode($code);

        $attenders = $registration->getAttenders()->toArray();
        $expected = 1;
        $actual = count($attenders);
        $this->assertEquals($expected,$actual);

        /**
         * @var $attender ScymAttender
         */
        $attender = $attenders[0];
        $meals = $attender->getMeals()->toArray();
        $expected = count($moreMeals);
        $actual = count($meals);

       // $registration = $manager->getRegistrationByCode($code);
        $this->assertNotNull($registration);
        $this->assertEquals($expected,$actual,'meal count wrong');

        $this->deleteRegistration($code);
    }

    private function deleteRegistration($code) {
        $manager = $this->getManager();
        $registration = $manager->getRegistrationByCode($code);
        if ($registration != null) {
            $manager->deleteRegistration($registration);
        }
    }

    private function createTestAttender($code, $persist=true) {
        $registrationDto = $this->getFakeRegistrationDto($code);
        $registration = new ScymRegistration();
        $registration->setName('Test Registration');
        $registration->setRegistrationCode($code);
        // $registration = ScymRegistration::createNewRegistration($registrationDto);
        // $attenderDto = new AttenderDto($this->getFakeAttender());
        $attender = new ScymAttender();
        $attender->setFirstName('Test');
        $attender->setLastName('Attender');
        $meal = new ScymMeal();
        $meal->setMealtime(72);
        $attender->addMeal($meal);
        // $attender = ScymAttender::CreateAttender($attenderDto);
        $registration->addAttender($attender);
        if ($persist) {
            $manager = $this->getManager();
            $manager->updateEntity($registration);
        }
        return $attender;
    }

    public function testGetAttender() {
        $code = 'UNITTEST_GETATTENDER';
        $this->deleteRegistration($code);
        /**
         * @var $attender ScymAttender
         */
        $attender = $this->createTestAttender($code);
        $attenderId = $attender->getAttenderId();
        $attender = null;
        $manager = $this->getManager();
        $attender = $manager->getAttender($attenderId);

        $actual = $attender->getAttenderId();
        $this->assertEquals($attenderId,$actual);

        $this->deleteRegistration($code);
    }

    public function testGetAgeGroupList() {
        $manager = $this->getManager();
        $list = $manager->getAgeGroupList();
        $this->assertNotNull($list);
        $this->assertNotEmpty($list);
    }

    public function testAttenderHousing() {
        $code = 'UNITTEST_GETATTENDERHOUSING';
        $this->deleteRegistration($code);
        /**
         * @var $attender ScymAttender
         */
        $attender = $this->createTestAttender($code);
        $attenderId = $attender->getAttenderId();
        $attender = null;
        $manager = $this->getManager();
        $attender = $manager->getAttender($attenderId);

        $actual = $attender->getAttenderId();
        $this->assertEquals($attenderId,$actual);

        /*****  sample units
        housingUnitId  unitname   housingTypeDescription        housingTypeCode
        -------------  ---------  ----------------------------  -----------------
        2  Cabin A2   Night Owl Dorm for Women      OWLW
        4  Cabin B2   Family Cabin                  FAMILY
        11  Cabin F1   Couples Cabin                 COUPLES
        27  Cabin J1   Family Cabin                  FAMILY
        53  Health 1   Health House (special needs)  HEALTH
        54  Health 10  Health House (special needs)  HEALTH
        55  Health 11  Health House (special needs)  HEALTH
        67  Motel 1    Camp Motel                    MOTEL
        87  Motel 6    Camp Motel                    MOTEL
        96  Tenting    Tenting                       TENT
        97  Avoden     Night Owl Dorm for Men        OWLM
         **/

        $assignment = ScymHousingAssignment::CreateAssignment(5,27);
        $attender->addHousingAssignment($assignment);

        $manager->updateEntity($attender);
        $this->assertNotEmpty($attender->getHousingAssignments());


        $this->deleteRegistration($code);

    }

    public function testGetPendingConfirmations() {
        $manager = $this->getManager();
        $actual = $manager->getRegistrationList('unconfirmed');
        $this->assertNotNull($actual);
    }

    public function testGetIncompleteHousing() {
        $manager = $this->getManager();
        $actual = $manager->getRegistrationList('incomplete');
        $this->assertNotNull($actual);
    }
    public function testAllHousing() {
        $manager = $this->getManager();
        $actual = $manager->getRegistrationList('allregistrations');
        $this->assertNotNull($actual);
    }

    public function testPersonHousing() {
        $manager = $this->getManager();
        $actual = $manager->getRegistrationList('name','Tony fish');
        $this->assertNotNull($actual);
    }
    public function testRegistrationCounts() {
        $manager = $this->getManager();
        $actual = $manager->getRegistrationCount();
        $this->assertNotNull($actual);
    }

    public function testAttendersView() {
        $manager = $this->getManager();
        $actual = $manager->getAttendersViewForRegistration(244);
        $this->assertNotNull($actual);
    }

    public function testHousingView() {
        $manager = $this->getManager();
        $actual = $manager->getHousingAssignmentView(244);
        $this->assertNotNull($actual);
    }

    public function testYouth() {
        $manager = $this->getManager();
        $testId = 178;
        $registration = $manager->getRegistration($testId);
        $this->assertNotNull($registration);
        $attenders = $registration->getAttenders();
        /**
         * @var $youth ScymYouth
         */
        $actualAttender = null;
        $actualYouth = null;
        foreach($attenders as $attender) {
            $manager->deleteYouth($attender);
        }
        $manager->updateEntity($registration);
        $registration = $manager->getRegistration($testId);
        $this->assertNotNull($registration);
        $attenders = $registration->getAttenders();
        $attenderId = 0;
        foreach($attenders as $attender) {
            /**
             * @var $attender ScymAttender
             */
            if ($attender->getGenerationId() > 1) {
                $youth = $attender->getYouth();
                $this->assertNull($youth,'Youth was not deleted');
                $attenderId = $attender->getAttenderId();
                $youth = $attender->createYouth();
                /*
                $youth = new ScymYouth();
                $youth->setGenerationId($attender->getGenerationId());
                $youth->setAttender($attender);
                $attender->setYouth($youth);
                // $manager->updateEntity($attender);
                */

                // $manager->updateEntity($registration);

                $this->assertNotNull($youth,'Youth was not created');
                break;
            }
        }

        $this->assertGreaterThan(0,$attenderId,'Youth not found');
        $dob = new \DateTime('2000-01-01');
        $youth->setDateofBirth($dob);
        $expectedDob = $dob->format('Y-m-d');
        $expectedFormsSubmitted = true;
        $youth->setFormsSubmitted(true);
        $expectedGradeLevel = '10';
        $youth->setGradeLevel('10');
        $manager->updateEntity($registration);

        $registration = $manager->getRegistration($testId); // 244);
        $this->assertNotNull($registration);
        $attenders = $registration->getAttenders();
        $actual = null;
        $found = false;
        foreach($attenders as $attender) {
            /**
             * @var $attender ScymAttender
             */
            if ($attender->getAttenderId() == $attenderId) {
                $found = true;
                $actual = $attender->getYouth();
                break;
            }
        }

        $this->assertTrue($found,'Attender not found');
        $this->assertNotNull($actual,'Youth not created.');
        $actualDob = $actual->getDateofBirth();
        $this->assertNotNull($actualDob,'DOB not saved.');
        $this->assertEquals($expectedDob,$actualDob->format('Y-m-d'),'DOB not equal');
        $this->assertEquals($expectedFormsSubmitted,$actual->getFormsSubmitted(),'Forms submitted not equal');
        $this->assertEquals($expectedGradeLevel,$actual->getGradeLevel(),'Grade level not equal.');

        $youthAttender = $youth->getAttender();
        $this->assertNotNull($youthAttender);
        $this->assertEquals($attenderId,$youthAttender->getAttenderId());
    }

    public function testGetYouthList() {
        $manager = $this->getManager();
        $actual = $manager->getYouthList();
        $this->assertNotNull($actual);

    }
}

