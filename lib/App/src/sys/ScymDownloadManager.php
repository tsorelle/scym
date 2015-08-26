<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/25/2015
 * Time: 1:59 PM
 */

namespace App\sys;


use Symfony\Component\HttpFoundation\Request;

class ScymDownloadManager
{
    public function getNewsletterList() {
        $manager = new \App\db\ScymDirectoryManager();
        return $manager->getEmailForNewsletter();
    }

    public function getAddressesList() {
        $request = Request::createFromGlobals();
        $directoryOnly = $request->request->has('directoryonly');
        $residenceOnly = $request->request->has('residenceonly');
        $manager = new \App\db\ScymDirectoryManager();
        $result = $manager->getAddressListForDownload($directoryOnly,$residenceOnly);

        return $result;


    }

    public function getContactsList() {
        $request = Request::createFromGlobals();

        $includeKids = $request->request->has('includekids');
        $directoryOnly = $request->request->has('contactdirectoryonly');
        $affiliation = $request->query->get('aff',null);
        $manager = new \App\db\ScymDirectoryManager();
        $result = $manager->getContactListForDownload($directoryOnly,$includeKids,$affiliation);

        return $result;
    }
}