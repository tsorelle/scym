<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/25/2015
 * Time: 1:59 PM
 */

namespace App\src;


use Symfony\Component\HttpFoundation\Request;

class ScymDownloadManager
{
    public function getNewsletterList() {
        $manager = new \App\db\ScymDirectoryManager();
        return $manager->getEmailForNewsletter();
    }

    public function getAddressesList() {
        $request = Request::createFromGlobals();

    }

    public function getContactsList() {
        $request = Request::createFromGlobals();

    }
}