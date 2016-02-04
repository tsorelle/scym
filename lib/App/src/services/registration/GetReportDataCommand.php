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

        switch($reportId) {
            case 'housing.requestCounts' :
                $results = $manager->getHousingRequestCountsReport();
                $this->setReturnValue($results);
                break;
            case 'housing.assignedCounts' :
                $results = $manager->getHousingAssignmentCounts();
                $this->setReturnValue($results);
                break;
            case 'housing.housingRoster' :
                $results = $manager->getHousingRoster();
                $this->setReturnValue($results);
                break;
            case 'housing.occupants' :
                $results = $manager->getOccupantsReport();
                $this->setReturnValue($results);
                break;
        }

    }
}