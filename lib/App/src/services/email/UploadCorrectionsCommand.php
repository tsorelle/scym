<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/22/2015
 * Time: 3:43 PM
 */

namespace App\services\email;


use App\db\ScymDirectoryManager;
use Tops\services\TServiceCommand;

class UploadCorrectionsCommand extends TServiceCommand
{
    public function __construct() {
        $this->addAuthorization('administer meeting directory');
    }


    protected function run()
    {
        $corrections = $this->getRequest();
        $manager = new ScymDirectoryManager();
        $updateCount = $manager->processEmailCorrections($corrections);
        $bounces = $manager->getEmailBounces();
        $this->setReturnValue($bounces);
        if ($updateCount == 0) {
            $this->addInfoMessage("No corrections were made. Records may have been already updated.");
        }
        else {
            $plural = ($updateCount > 1) ? 's were' : ' was';
            $this->addInfoMessage("$updateCount correction$plural made");
        }
    }
}