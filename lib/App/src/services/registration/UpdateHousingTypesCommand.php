<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/25/2016
 * Time: 8:00 AM
 */

namespace App\services\registration;


use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

class UpdateHousingTypesCommand extends TServiceCommand
{
    public function __construct() {
        $this->addAuthorization("administer housing");
    }

    protected function run()
    {
        $request = $this->getRequest();
        if ($request == null) {
            $this->addErrorMessage('ERROR: No request recieved.');
            return;
        }
        $added = isset($request->added) ? $request->added : array();
        $updated = isset($request->updated) ? $request->updated : array();

        $updatedCount = count($updated);
        $addedCount = count($added);
        $expectedResultCount = $addedCount + $updatedCount;
        if ($expectedResultCount == 0) {
            $this->addWarningMessage('No housing types added or updated.');
            return;
        }
        $manager = new ScymRegistrationsManager();
        $resultCount = $manager->updateHousingTypes($updated, $added);

        if ($resultCount == $expectedResultCount) {
            if ($addedCount > 0) {
                $this->addInfoMessage("$addedCount housing type" . ($addedCount == 1 ? ' was added.' : "s were added"));
            }
            if ($updatedCount > 0) {
                $this->addInfoMessage("$updatedCount housing type" . ($updatedCount == 1 ? ' was updated.' : "s were updated"));
            }
        }
        else {
            if ($resultCount == 0) {
                $this->addErrorMessage("Error: No items were updated.");
            }
            else {
                $this->addErrorMessage('Updates failed for some items.');
            }
        }

        $result = $manager->getHousingTypesEditList();
        $this->setReturnValue($result);

    }
}