<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/22/2015
 * Time: 4:27 PM
 */

namespace App\services\email;


use App\db\ScymDirectoryManager;
use Tops\services\TServiceCommand;

class CorrectBouncesCommand extends TServiceCommand
{
    public function __construct() {
        $this->addAuthorization('administer meeting directory');
    }

    /**
     * @var BounceListItem[]
     */
    private $bounceCorrections;

    protected function run()
    {
        $this->bounceCorrections = $this->getRequest();
        $manager = new ScymDirectoryManager();
        $updateCount = 0;
        if (!empty($this->bounceCorrections)) {
            foreach($this->bounceCorrections as $bounceItem) {
                $person = $manager->getPersonById($bounceItem->personId);
                if (!empty($person)) {
                    $updateCount++;
                    $person->setEmail($bounceItem->correction);
                    $person->setNewsletter(1);
                }
            }
        }

        $this->addInfoMessage('Updates complete.');
        if ($updateCount == 0) {
            $this->addInfoMessage("No corrections were made. Records may have been already updated.");
        }
        else {
            $manager->saveChanges();
            $plural = ($updateCount > 1) ? 's were' : ' was';
            $this->addInfoMessage("$updateCount correction$plural made");
        }
    }
}