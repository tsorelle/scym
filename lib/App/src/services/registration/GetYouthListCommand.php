<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/15/2016
 * Time: 7:28 AM
 */

namespace App\services\registration;


use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

class GetYouthListCommand extends TServiceCommand
{
    public function __construct() {
        $this->addAuthorization("administer youth");
    }

    protected function run()
    {
        $manager = new ScymRegistrationsManager();
        $list = $manager->getYouthList();
        $this->setReturnValue($list);
    }
}