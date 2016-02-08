<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/2/2016
 * Time: 7:34 AM
 */

namespace App\services\registration;


use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

class GetReportDataCommand extends TServiceCommand
{

    protected function run()
    {
        $trapExceptions = true;
        $request = $this->getRequest();
        if ($request == null) {
            $this->addErrorMessage('ERROR: No request recieved.');
            return;
        }
        $reportId = isset($request->id) ? $request->id : null;
        if ($reportId == null) {
            $this->addErrorMessage('ERROR: No report id recieved.');
            return;
        }
        $params = isset($request->params) ?  explode(';',$request->params) : array();
        $manager = new ScymRegistrationsManager();

        try {
            switch ($reportId) {
                case 'housing.requestCounts' :
                    $results = $manager->getHousingRequestCountsReport();
                    break;
                case 'housing.assignedCounts' :
                    $results = $manager->getHousingAssignmentCounts();
                    break;
                case 'housing.housingRoster' :
                    $results = $manager->getHousingRoster();
                    break;
                case 'housing.occupants' :
                    $results = $manager->getOccupantsReport();
                    break;
                case 'admin.mealCounts' :
                    $results = $manager->getMealCountsReport();
                    break;
                case 'admin.mealRoster' :
                    $results = new \stdClass();
                    $session = $manager->getSession();
                    $now = new \DateTime();
                    $results->sessionStarted = ($session->getStart() <= $now);
                    $results->attenders = $manager->getMealRosterReport();
                    break;
                case 'admin.registrationsReceived' :
                    $results = $manager->getRegistrationsReceivedReport();
                    break;
                case 'admin.registeredAttenders' :
                    $results = $manager->getRegisteredAttendersReport();
                    break;
                case 'admin.attendersByArrival' :
                    $results = $manager->getAttendersByArrivalReport();
                    break;
                case 'admin.attenderNotes' :
                    $results = $manager->getAttenderNotesReport();
                    break;
                case 'admin.paymentsReceived' :
                    $results = $manager->getPaymentsReceivedReport();
                    break;
                case 'admin.miscCounts' :
                    $results = $manager->getMiscCountsReport();
                    break;
                case 'admin.financialAid' :
                    $results = $manager->getFinancialAidReport();
                    break;
                case 'admin.ledger' :
                    $results = $manager->getLedgerReport();
                    break;
                default :
                    $this->addErrorMessage("ERROR: Invalid report id '$reportId'");
                    $results = null;
                    break;
            }
        }
        catch(\Exception $ex) {
            if (!$trapExceptions) {
                throw $ex;
            }
            $this->addErrorMessage("ERROR: ".$ex->getMessage());
        }
        $this->setReturnValue($results);

    }
}