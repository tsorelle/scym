<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/24/2015
 * Time: 9:26 AM
 */
use App\services\registration\GetRegistrationCostCommand;

class RegistrationCommandTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        \Tops\sys\TObjectContainer::Clear();
        \Tops\sys\TObjectContainer::Register('configManager', '\Tops\sys\TYmlConfigManager');
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
    public function testGetCostCommand() {

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

        $donation = new \stdClass(); // fake
        $donation->Key = 2;
        $donation->Value = 100;

        $request = new \stdClass();
        $request->aidRequested  = 200;
        $request->attenders = array(
            $attender
        );
        $request->deletedAttenders = array();
        $request->donations = array($donation);
        $request->getFundList = 1;

        $command = new GetRegistrationCostCommand();
        $actual = $command->runTest($request);

        $this->assertNotNull($actual);

    }
}
