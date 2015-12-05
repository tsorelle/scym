<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/30/2015
 * Time: 12:24 PM
 */

namespace Tops\test\unit\scym;


use App\db\api\AttenderDto;
use App\db\scym\ScymDonation;
use App\db\ScymAccountManager;
use App\db\ScymRegistrationsManager;
use Tops\sys\TKeyValuePair;
use Tops\sys\TNameValuePair;

class ScymAccountManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        \Tops\sys\TObjectContainer::Clear();
        \Tops\sys\TObjectContainer::Register('configManager', '\Tops\sys\TYmlConfigManager');
    }

    private $regManager;

    private function getManager()
    {
        if (!isset( $this->regManager)) {
            \Tops\sys\TObjectContainer::Clear();
            \Tops\sys\TObjectContainer::Register('configManager', '\Tops\sys\TYmlConfigManager');
            $this->regManager = new ScymRegistrationsManager();
        }

        return  new ScymAccountManager($this->regManager);
    }




    private function getFakeAttender()
    {
        $attender = new \stdClass();
        $attender->attenderId = 1;
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
        $attender->ageGroupId = null; // lookup agegroups
        $attender->creditTypeId = 0; // number; // formerly: feeCredit; lookup: creditTypes
        $attender->meals = array(
            43,
            51,
            52,
            53,
            61,
            62,
            63,
            71,
            72
        );
        return $attender;
    }

    public function testRegistrationCalculation1()
    {
        $manager = $this->getManager();
        $attender = $this->getFakeAttender();
        $attenders = array(new AttenderDto($attender));
        $donations = array();
        $aidRequested = 1000.00;

        $account = $manager->createAccount($attenders,$donations,$aidRequested);
        $this->assertNotNull($account);
        $expected = 235.00;
        $actual = $account->getChargesTotal();
        $this->assertEquals($expected,$actual,'Charges total incorrect');
        $expected = 0;
        $actual = $account->getBalance();
        $this->assertEquals($expected,$actual,'Balance incorrect');
    }

    public function testRegistrationCalculation3()
    {
        $manager = $this->getManager();
        $attender1 = $this->getFakeAttender();
        $attender1->housingTypeId = 6;

        $attender2 = $this->getFakeAttender();
        $attender2->housingTypeId = 6;
        $attender2->firstName = 'Liz';
        $attender2->linens = 1;

        // same as test two except apply teacher credit
        $attender2->creditTypeId = 2; // number; // formerly: feeCredit; lookup: creditTypes

        $attender3 = $this->getFakeAttender();
        $attender3->firstName = 'Sam';
        $attender3->generationId = 2;
        $attender3->housingTypeId = 14;

        $attenders = array(
            new AttenderDto($attender1),
            new AttenderDto($attender2),
            new AttenderDto($attender3)
        );


        $donations = array();
        $aidRequested = 1000.00;

        $account = $manager->createAccount($attenders,$donations,$aidRequested);
        $this->assertNotNull($account);
        // $expected = 309.00;
        // $actual = $account->getChargesTotal();
        // $this->assertEquals($expected,$actual,'Charges total incorrect');
        $expected = 9;
        $actual = $account->getBalance();
        $this->assertEquals($expected,$actual,'Balance incorrect');
    }

    public function testRegistrationCalculation3a()
    {
        // Test 3 variation add donations

        $manager = $this->getManager();
        $attender1 = $this->getFakeAttender();
        $attender1->housingTypeId = 6;

        $attender2 = $this->getFakeAttender();
        $attender2->housingTypeId = 6;
        $attender2->firstName = 'Liz';
        $attender2->linens = 1;

        // same as test two except apply teacher credit
        $attender2->creditTypeId = 2; // number; // formerly: feeCredit; lookup: creditTypes

        $attender3 = $this->getFakeAttender();
        $attender3->firstName = 'Sam';
        $attender3->generationId = 2;
        $attender3->housingTypeId = 14;

        $attenders = array(
            new AttenderDto($attender1),
            new AttenderDto($attender2),
            new AttenderDto($attender3)
        );

        $donation1 = TKeyValuePair::Create(2,20.00);
        $donation2 = TKeyValuePair::Create(3,10.0);

        $donations = array($donation1,$donation2);
        $aidRequested = 0.0; // 1000.00;

        $account = $manager->createAccount($attenders,$donations,$aidRequested);
        $this->assertNotNull($account);
        $expected = 300.00;
        $actual = $account->getAidEligibility();
        $this->assertEquals($expected,$actual,'Aid eligibility incorrect');

        $expected = 339.0;
        $actual = $account->getBalance();
        $this->assertEquals($expected,$actual,'Balance incorrect');
    }
    public function testRegistrationCalculation3b()
    {
        // Test 3 variation add donations, request aid

        $manager = $this->getManager();
        $attender1 = $this->getFakeAttender();
        $attender1->housingTypeId = 6;

        $attender2 = $this->getFakeAttender();
        $attender2->housingTypeId = 6;
        $attender2->firstName = 'Liz';
        $attender2->linens = 1;

        // same as test two except apply teacher credit
        $attender2->creditTypeId = 2; // number; // formerly: feeCredit; lookup: creditTypes

        $attender3 = $this->getFakeAttender();
        $attender3->firstName = 'Sam';
        $attender3->generationId = 2;
        $attender3->housingTypeId = 14;

        $attenders = array(
            new AttenderDto($attender1),
            new AttenderDto($attender2),
            new AttenderDto($attender3)
        );

        $donation1 = TKeyValuePair::Create(2,20.00);
        $donation2 = TKeyValuePair::Create(3,10.0);

        $donations = array($donation1,$donation2);
        $aidRequested = 1000.00;

        $account = $manager->createAccount($attenders,$donations,$aidRequested);
        $this->assertNotNull($account);
        $expected = 300.00;
        $actual = $account->getAidEligibility();
        $this->assertEquals($expected,$actual,'Aid eligibility incorrect');

        $expected = 39.0;
        $actual = $account->getBalance();
        $this->assertEquals($expected,$actual,'Balance incorrect');
    }
}
