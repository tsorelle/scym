<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/8/2015
 * Time: 10:18 AM
 */

namespace App\services\registration;


use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

class GetAttenderCommand extends TServiceCommand
{


    /**
     *  ------ Request ----------
     *
     *  export interface IGetAttenderRequest {
     *      id : any;
     *      includeLookups : number;
     *  }
     *
     *  ------ Response ----------
     *
     *  export interface IGetAttenderResponse {
     *      attender: IAttender;
     *      lookups: IAttenderLookups;
     *  }
     *
     *  export interface IAttenderLookups {
     *      housingTypes: IHousingTypeListItem[];
     *      affiliationCodes : IListItem[];
     *      ageGroups : IAgeGroup[];
     *  }
     *
     *  export interface IListItem {
     *      Text: string;
     *      Value: any;
     *      Description: string;
     *  }
     *
     *  export interface IAgeGroup extends IListItem {
     *      cutoffAge : any;
     *  }
     *
     *  export interface IHousingTypeListItem extends IListItem {
     *      category: any;
     *  }
     *
     */
    protected function run()
    {
        $request = $this->getRequest();
        if ($request == null) {
            throw new \Exception('No request recieved for GetAttenderCommand.');
        }
        $attenderId = isset($request->id) ? $request->id : null;
        if (!$attenderId) {
            throw new \Exception('No attender id.');
        }
        $includeLookups = isset($request->includeLookups) ? $request->includeLookups : false;

        $manager = new ScymRegistrationsManager();


    }
}