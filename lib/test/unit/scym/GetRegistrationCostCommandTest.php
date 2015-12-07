<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/24/2015
 * Time: 9:26 AM
 */
use App\services\registration\GetRegistrationCostCommand;
use App\services\registration\SaveRegistrationChangesCommand;

class RegistrationCommandTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        \Tops\sys\TObjectContainer::Clear();
        \Tops\sys\TObjectContainer::Register('configManager', '\Tops\sys\TYmlConfigManager');
    }

    private function getFakeAttender()
    {
        $attender = new \stdClass(); // fake
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

    private function getFakeRequest()
    {

        $request = new \stdClass();
        $request->attenders = array();
        $request->deletedAttenders = array();
        $request->donations = array();
        $request->getFundList = 0;
        $request->aidRequested = 0.0;
        return $request;
    }

    /**
     *  Request
     *     export interface ICostUpdateRequest {
     *     aidRequested  : any;
     *     attenders : IAttender[];
     *     deletedAttenders : any[];
     *     donations: IKeyValuePair[];
     *     getFundList: number;
     *    }
     */
    public function testGetCostCommand()
    {
        // test 1
        $request = $this->getFakeRequest();

        $request->getFundList = 1;
        $attender = $this->getFakeAttender();
        array_push($request->attenders, $attender);

        $command = new GetRegistrationCostCommand();
        $actual = $command->runTest($request);

        $this->assertNotNull($actual);

        $expected = '$235.00';
        $this->assertEquals($expected, $actual->balance);

    }

    public function testGetCostCommand2()
    {
        // apply financial aid

        $request = $this->getFakeRequest();
        $attender1 = $this->getFakeAttender();
        $attender1->housingTypeId = 6;

        $attender2 = $this->getFakeAttender();
        $attender2->housingTypeId = 6;
        $attender2->firstName = 'Liz';
        $attender2->linens = 1;

        $attender3 = $this->getFakeAttender();
        $attender3->firstName = 'Sam';
        $attender3->generationId = 2;
        $attender3->housingTypeId = 14;

        $request->attenders = array($attender1,$attender2,$attender3);

        $command = new GetRegistrationCostCommand();
        $actual = $command->runTest($request);
        $this->assertNotNull($actual);
        $expected = '$574.00';
        $this->assertEquals($expected, $actual->balance);

    }

    public function testGetCostCommand3()
    {
        // apply financial aid

        $request = $this->getFakeRequest();
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

        $request->attenders = array($attender1,$attender2,$attender3);

        $command = new GetRegistrationCostCommand();
        $actual = $command->runTest($request);
        $this->assertNotNull($actual);
        $expected = '$309.00';
        $this->assertEquals($expected, $actual->balance);
        $expected = '$300.00';
        $this->assertEquals($expected, $actual->aidEligibility);

    }

    public function testGetCostCommand4()
    {
        // apply financial aid

        $request = $this->getFakeRequest();
        $attender1 = $this->getFakeAttender();
        $attender1->housingTypeId = 1;
        $attender1->arrivalTime = 51;
        $attender1->departureTime = 63;
        $attender1->meals = array(
            51,
            52,
            53,
            61,
            62,
            63
        );


        $attender2 = clone $attender1;
        $attender2->firstName = 'Sam';
        $attender2->generationId = 2;

        $request->attenders = array($attender1,$attender2);

        $command = new GetRegistrationCostCommand();
        $actual = $command->runTest($request);
        $this->assertNotNull($actual);
        $expected = '$132.00';
        $this->assertEquals($expected, $actual->balance);

    }

    public function testGetCostCommand5() {
        $request = $this->getFakeRequest();
        $attender = $this->getFakeAttender();
        $attender->housingTypeId = 10;
        array_push($request->attenders, $attender);

        $command = new GetRegistrationCostCommand();
        $actual = $command->runTest($request);
        $this->assertNotNull($actual);
        $expected = '$310.00';
        $this->assertEquals($expected, $actual->balance);
    }

    public function testGetCostCommand6() {
        $request = $this->getFakeRequest();
        $attender = $this->getFakeAttender();
        $attender->housingTypeId = 10;
        $attender->specialNeedsTypeId = 2;

        array_push($request->attenders, $attender);

        $command = new GetRegistrationCostCommand();
        $actual = $command->runTest($request);
        $this->assertNotNull($actual);
        $expected = '$265.00';
        $this->assertEquals($expected, $actual->balance);
    }

    public function testGetCostCommand7() {
        $request = $this->getFakeRequest();
        $attender = $this->getFakeAttender();
        $attender->housingTypeId = 9;
        $attender->singleOccupant = 1;

        array_push($request->attenders, $attender);

        $command = new GetRegistrationCostCommand();
        $actual = $command->runTest($request);
        $this->assertNotNull($actual);
        $expected = '$350.00';
        $this->assertEquals($expected, $actual->balance);
        $expected = '$265.00';
        $this->assertEquals($expected, $actual->aidEligibility);

    }


}