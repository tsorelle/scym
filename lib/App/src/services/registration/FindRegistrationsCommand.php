<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/3/2016
 * Time: 6:54 AM
 */

namespace App\services\registration;


use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

class FindRegistrationsCommand extends TServiceCommand
{

    protected function run()
    {
        $request = $this->getRequest();
        if ($request == null) {
            $this->addErrorMessage("System Error: no request recieved.");
            return;
        }
        if (!isset($request->searchType)) {
            $this->addErrorMessage("System Error: no search type.");
            return;
        }

        $searchValue = isset($request->searchValue) ? $request->searchValue : null;
        $manager = new ScymRegistrationsManager();
        $list = $manager->getRegistrationList($request->searchType,$searchValue);
        $this->setReturnValue($list);

    }
}