<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/26/2015
 * Time: 6:37 AM
 */

namespace App\services\directory;


use App\db\ScymDirectoryManager;
use Tops\services\TServiceCommand;

class GetAffiliationsListCommand extends TServiceCommand
{
    protected function run()
    {
        $result = new InitDirectoryResponse();
        $manager = new ScymDirectoryManager();
        $result = $manager->getAffiliationCodeList();
        $this->setReturnValue($result);
    }

}