<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/16/2016
 * Time: 12:05 PM
 */

namespace App\services\registration;


use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

class UpdateYouthInfoCommand extends TServiceCommand
{
    /**
     * Request contract:
     *  export interface IUpdateYouthRequest {
            attenderId : number;
            sponsor : string;
            ageGroupId : number;
            youthNotes : string;
            formsSubmitted: boolean;
        }
     */

    public function __construct() {
        $this->addAuthorization("administer youth");
    }

    protected function run()
    {
        $request = $this->getRequest();
        if (empty($request)) {
            $this->addErrorMessage("Error: No service request");
            return;
        }
        $id = isset($request->youthId) ? $request->youthId : null;
        if ($id == null) {
            $this->addErrorMessage("Error: No youth info id");
            return;
        }
        $sponsor = isset($request->sponsor ) ? $request->sponsor : null;
        $ageGroupId = isset($request->ageGroupId ) ? $request->ageGroupId : null;
        $youthNotes = isset($request->youthNotes ) ? $request->youthNotes : null;
        $formsSubmitted = isset($request->formsSubmitted ) ? $request->formsSubmitted : null;

        $manager = new ScymRegistrationsManager();
        $youth = $manager->getYouth($id);
        if ($youth == null) {
            $this->addErrorMessage("Error youth not found for youthId #$id");
            return;
        }

        if($sponsor != null) {
            $youth->setSponsor($sponsor);
        }
        if($ageGroupId != null) {
            $youth->setAgeGroupId($ageGroupId);
        }
        if($youthNotes != null) {
            $youth->setNotes($youthNotes);
}
        if($formsSubmitted != null) {
            $youth->setFormsSubmitted($formsSubmitted);
        }

        $manager->updateEntity($youth);

        $list = $manager->getYouthList();
        $this->setReturnValue($list);

    }
}