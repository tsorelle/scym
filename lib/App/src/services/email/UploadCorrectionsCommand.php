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
        $bounces = array();
        $corrections = $this->getRequest();
        $manager = new ScymDirectoryManager();
        $updateCount = 0;
        foreach($corrections as $correction) {
            $address = trim($correction->address);
            $status = trim($correction->status);
            if ((!empty($address)) && (!empty($status))) {
                $persons = $manager->getPersonByEmail($address);
                foreach($persons as $person) {
                    $updateCount++;
                    $person->setNewsletter(0);
                    if ($status != 'unsubscribed') {
                        $bounceItem = new BounceListItem();
                        $bounceItem->personId = $person->getPersonid();
                        $bounceItem->name = $person->getFullName();
                        $bounceItem->email = $address;
                        $bounceItem->correction = '';
                        $bounceItem->validation = '';
                        array_push($bounces,$bounceItem);
                        // invalid email
                        $person->setEmail('');
                    }
                    $manager->persistEntity($person);
                }
            }
        }
        $this->addInfoMessage('Update completed.');
        if ($updateCount) {
            $plural = ($updateCount > 1) ? 's were' : ' was';
            $this->addInfoMessage("$updateCount correction$plural made");
            $manager->saveChanges();
        }
        $this->setReturnValue($bounces);
    }

}