<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/6/2015
 * Time: 2:07 PM
 */

namespace App\services\directory;
use App\db\scym\ScymAddress;
use App\db\ScymDirectoryManager;
use Tops\services;
use Tops\services\TServiceCommand;


class UpdateAddressCommand extends TServiceCommand
{
    public function __construct() {
        $this->addAuthorization("administer directory");
    }

    protected function run()
    {
        $request = $this->getRequest();
        $manager = new ScymDirectoryManager();
        
        $id = $request->addressId;
        $address = null;
        if (empty($id)) {
            $address = new Scymaddress();
        }
        else {
            $address = $manager->getaddressById($id);
            if (empty($address)) {
                $this->addErrorMessage('address not found for id ' . $request->Value);
                return;
            }
        }
        $address->updateFromDataTransferObject($request);
        $manager->updateEntity($address);
        $response = $address->getDataTransferObject();
        $this->setReturnValue($response);
    }
}