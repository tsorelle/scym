<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/25/2015
 * Time: 1:59 PM
 */

namespace App\sys;


use App\db\ScymRegistrationsManager;
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
        $newsletter = $request->request->has('newsletter');
        $manager = new \App\db\ScymDirectoryManager();
        $result = $manager->getAddressListForDownload($directoryOnly,$residenceOnly,$newsletter);

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

    public function getTransactionsList() {
        $request = Request::createFromGlobals();
        $attendedOnly = $request->get('attended') !== null;
        $manager = new ScymRegistrationsManager();
        $transactions = $manager->getTransactionsDownload($attendedOnly);
        $result = array();
        $header = 'year,registrationId,registrationCode,Name,TransactionType,Entry,Description,Amount'."\n";
        array_push($result,$header);
        foreach($transactions as $transaction) {
            $record =
                $this->fieldValue($transaction->year,'number').','.
                $this->fieldValue($transaction->registrationId,'number').','.
                $this->fieldValue($transaction->registrationCode).','.
                $this->fieldValue($transaction->Name).','.
                $this->fieldValue($transaction->TransactionType).','.
                $this->fieldValue($transaction->Entry).','.
                $this->fieldValue($transaction->Description).','.
                $this->fieldValue($transaction->Amount,'number')."\n";
            array_push($result,$record);
        }
        return $result;
    }

    public function getAttenderCounts() {
        $request = Request::createFromGlobals();

        $attendedOnly = $request->get('attended') !== null;
        $manager = new ScymRegistrationsManager();
        $attenders = $manager->getAttenderCountsDownload($attendedOnly);
        $result = array();
        $header =
            'Year,AttenderId,RegistrationId,Attended,Name,Arrival,Departure,Generation,Role,Days,'.
            'Nights,DormNights,CabinNights,Meals,DayVisitor'."\n";
        array_push($result,$header);
        foreach($attenders as $attender) {
            $record =
                $this->fieldValue($attender->year,'number').','.
                $this->fieldValue($attender->attenderId,'number').','.
                $this->fieldValue($attender->registrationId,'number').','.
                $this->fieldValue($attender->attended).','.
                $this->fieldValue($attender->Name).','.
                $this->fieldValue($attender->Arrival).','.
                $this->fieldValue($attender->Departure).','.
                $this->fieldValue($attender->Generation).','.
                $this->fieldValue($attender->Role).','.
                $this->fieldValue($attender->Days,'number').','.
                $this->fieldValue($attender->Nights,'number').','.
                $this->fieldValue($attender->DormNights,'number').','.
                $this->fieldValue($attender->CabinNights,'number').','.
                $this->fieldValue($attender->Meals,'number').','.
                $this->fieldValue($attender->DayVisitor)."\n";
            array_push($result,$record);
        }
        return $result;
    }

    public function getRegistrationCounts() {
        $manager = new ScymRegistrationsManager();
        $registrations = $manager->getRegistrationCountsDownload();
        $result = array();
        $header =
            'year, RegId, LookupCode, Name, Attenders, Adults, Youth, Meals, Linens, AdultDays, '.
            'YouthDays, AdultDormNights, YouthDormNights, AdultCabinNights, YouthCabinNights, Fees, '.
            'Donations, Credits, Payments, Balance'."\n";
        array_push($result,$header);
        foreach($registrations as $registration) {
            $record =
                $this->fieldValue($registration->year,'number').','.
                $this->fieldValue($registration->registrationId,'number').','.
                $this->fieldValue($registration->registrationCode).','.
                $this->fieldValue($registration->Name).','.
                $this->fieldValue($registration->Attenders,'number').','.
                $this->fieldValue($registration->Adults,'number').','.
                $this->fieldValue($registration->Youth,'number').','.
                $this->fieldValue($registration->Meals,'number').','.
                $this->fieldValue($registration->Linens,'number').','.
                $this->fieldValue($registration->AdultDays,'number').','.
                $this->fieldValue($registration->YouthDays,'number').','.
                $this->fieldValue($registration->AdultDormNights,'number').','.
                $this->fieldValue($registration->YouthDormNights,'number').','.
                $this->fieldValue($registration->AdultCabinNights,'number').','.
                $this->fieldValue($registration->YouthCabinNights,'number').','.
                $this->fieldValue($registration->Fees,'number').','.
                $this->fieldValue($registration->Donations,'number').','.
                $this->fieldValue($registration->Credits,'number').','.
                $this->fieldValue($registration->Payments,'number').','.
                $this->fieldValue($registration->Balance,'number')."\n";
            array_push($result,$record);
        }
        return $result;
    }

    public function getBalanceSheet() {
        $request = Request::createFromGlobals();

        $attendedOnly = $request->get('attended') !== null;
        $manager = new ScymRegistrationsManager();
        $registrations = $manager->getBalanceSheetDownload($attendedOnly);
        $result = array();
        $header = 'year,registrationId,registrationCode,Name,attendedCount,Fees,Donations,Credits,Payments,Balance,attended'."\n";

        array_push($result,$header);
        foreach($registrations as $registration) {
            $record =
                $this->fieldValue($registration->year,'number').','.
                $this->fieldValue($registration->registrationId,'number').','.
                $this->fieldValue($registration->registrationCode).','.
                $this->fieldValue($registration->Name).','.
                $this->fieldValue($registration->attendedCount,'number').','.
                $this->fieldValue($registration->Fees,'number').','.
                $this->fieldValue($registration->Donations,'number').','.
                $this->fieldValue($registration->Credits,'number').','.
                $this->fieldValue($registration->Payments,'number').','.
                $this->fieldValue($registration->Balance,'number').','.
                $this->fieldValue($registration->attended)."\n";

            array_push($result,$record);
        }
        return $result;
    }

    public function getFinanceSummary() {
        $manager = new ScymRegistrationsManager();
        $items = $manager->getFinanceSummaryDownload();
        $result = array();
        $header = 'ItemGroup,Item,Amount,Count'."\n";

        array_push($result,$header);
        foreach($items as $item) {
            $record =
                $this->fieldValue($item->ItemGroup,'number').','.
                $this->fieldValue($item->Item).','.
                $this->fieldValue($item->Amount,'number').','.
                $this->fieldValue($item->Count,'number')."\n";
            array_push($result,$record);
        }
        return $result;
    }

    public function getCampReport() {
        $manager = new ScymRegistrationsManager();
        $items = $manager->getCampReportDownload();
        $result = array();
        $header = 'Item,Count'."\n";

        array_push($result,$header);
        foreach($items as $item) {
            $record =
                $this->fieldValue($item->Item).','.
                $this->fieldValue($item->Count,'number')."\n";
            array_push($result,$record);
        }
        return $result;
    }

    public function getNameTags($filter='',$order='') {
        $request = Request::createFromGlobals();
        $filter = $request->get('filter');
        $order = $request->get('order');
        $manager = new ScymRegistrationsManager();
        $items = $manager->getNameTags($filter,$order);
        $result = array();
        $header = 'Name,Affiliation,First-Timer'."\n";

        array_push($result,$header);
        foreach($items as $item) {
            $record =
                $this->fieldValue($item->Name).','.
                $this->fieldValue($item->Affiliation).','.
                $this->fieldValue($item->firstTimer)."\n";
            array_push($result,$record);
        }
        return $result;
    }

    private function fieldValue($value,$type = 'string') {
        $result = $value === null ? '' : $value;
        if ($type == 'string') {
            $result = '"'.str_replace('""','"',$result).'"';
        }
        return $result;
    }
}