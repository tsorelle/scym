<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/28/2015
 * Time: 10:21 AM
 */

namespace App\services\email;


use App\db\ScymDirectoryManager;
use Tops\services\TServiceCommand;


class InitEmailListManagementCommand extends TServiceCommand
{

    protected function run()
    {
        $manager = new ScymDirectoryManager();
        $result = $manager->getEmailBounces();
        $this->setReturnValue($result);
    }}