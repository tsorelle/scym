<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/18/2016
 * Time: 10:33 AM
 */

namespace App\services\registration;

use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

/**
 * Class UpdateAgeGroupsCommand
 * @package App\services\registration
 *
 * Service contract
 *    updateAssignments : boolen
 *    updates [] of
 *       id : number
 *       cutoff: number
 *       active: boolean
 *  Returns agegroup list
 */

class UpdateAgeGroupsCommand extends TServiceCommand
{

    protected function run()
    {
        $test = false;

        $request = $this->getRequest();
        if ($request == null) {
            $this->addErrorMessage("ERROR: No request");
            return;
        }

        $updateAssignments = isset($request->updateAssignments) ? $request->updateAssignments : false;
        $updates = isset($request->updates) ? $request->updates : array();
        $manager = new ScymRegistrationsManager();

        $cutoffs = array();
        foreach ($updates as $update) {
            if (!(isset($update->id) && isset($update->cutoff))) {
                $this->addErrorMessage("Error: invalid update item in list.");
                return;
            }
            if (!empty($update->active)) {
                if (in_array($update->cutoff,$cutoffs)) {
                    $this->addErrorMessage("Error: duplicate cutoff day in update list.");
                    return;
                }
                array_push($cutoffs,$update->cutoff);
            }
        }

        $updated = $manager->updateAgeGroups($updates);
        if (!$updated) {
            $this->addErrorMessage("Error: Some updates cound not be processesd.");
            return;
        }
        $assignmentUpdateCount = 0;
        if ($updateAssignments) {
            $assignmentUpdateCount = $manager->reassignYouthAgeGroups($updateAssignments,$test);
        }

        if (!empty($request->updates)) {
            $this->addInfoMessage("Updated ".count($request->updates).' age groups.');
        }

        if ($assignmentUpdateCount > 0) {
            $this->addInfoMessage("Reassigned age group for $assignmentUpdateCount youth attenders.");
        }

        $result = $manager->getAgeGroupList(true);
        $this->setReturnValue($result);
    }
}