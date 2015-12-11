<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/2/2015
 * Time: 8:46 AM
 */

namespace App\services\registration;


use App\db\scym\ScymAddress;
use App\db\scym\ScymPerson;
use App\db\ScymDirectoryManager;
use Tops\services\TServiceCommand;
use Tops\sys\TDateTime;

class FindRegistrationAddressCommand extends TServiceCommand
{

    /**
     * Response:
     *   export interface INameValuePair {
     *      Name: string;
     *      Value: string;
     *   }
     *   export interface IFamilyAttender extends INameValuePair {
     *       firstName: string;
     *       lastName: string;
     *       middleName: string;
     *       generation: number;
     *       dateOfBirth: string;
     *   }
     *   export interface IFindRegistrationAddressResponse {
     *       name: string;
     *       address: string;
     *       city: string;
     *       persons: IFamilyAttender[];
     *   }
     */

    protected function run()
    {
        $request = $this->getRequest();
        $response = new \stdClass();
        $manager = new ScymDirectoryManager();

        $address = $manager->getAddressById($request);
        if (empty($address)) {
            $this->addErrorMessage('Address not found for id  ' . $request->Value);
        }

        $response->name  = $address->getAddressname();
        $response->address = $this->formatAddress($address);
        $response->city = $this->formatCity($address);
        $response->persons = $this->getPersons($address);

        $this->setReturnValue($response);
    }

    private function formatAddress(ScymAddress $address)
    {
        $result = $address->getAddress1();
        if ($result == null) {
            $result = '';
        }
        $address2 = $address->getAddress2();
        if (!empty($address2)) {
            $result .= ", $address2";
        }
        return $result;
    }

    private function formatCity(ScymAddress $address)
    {
        $result = $address->getCity();
        $state = $address->getState();
        $zip = $address->getPostalcode();
        $country = $address->getCountry();

        if ($state) {
            $result .= ",$state";
        }
        if ($zip) {
            $result .= " $zip";
        }
        if ($country) {
            $result .= " $country";
        }
        return $result;
    }

    private function getPersons(ScymAddress $address) {
        $result = array();
        $persons = $address->getPersons();
        foreach($persons as $person) {
            /**
             * @var $person ScymPerson
             */
            $dto = new \stdClass();
            $dto->Name  =  	 $person->getFullName();
            $dto->Value =       $person->getPersonid();
            $dto->firstName =   $person->getFirstname();
            $dto->lastName =    $person->getLastname();
            $dto->middleName =  $person->getMiddlename();
            $dto->generation =  $person->getGeneration();
            if ($dto->generation == 4 || $dto->generation == 3) {
                $dto->generation = 2; // Young Friend and Child not supported, promote them to Youth
            }
            $dob = $person->getDateOfBirth();
            $dto->dateOfBirth = TDateTime::isEmpty($dob) ? '' : $dob->format('m/d/Y');
            array_push($result,$dto);
        }
        return $result;
    }

}