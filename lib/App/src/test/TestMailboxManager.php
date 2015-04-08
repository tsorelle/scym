<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/14/2015
 * Time: 11:10 AM
 */

namespace App\test;
use Tops\sys\TMemoryMailboxManager;
use Tops\sys\TPath;
use Tops\sys\TPostOffice;


class TestMailboxManager extends TMemoryMailboxManager
{
    public function __construct()
    {
        parent::__construct();
        $this->Load();
    }

    public function Load() {
        $this->clearMailboxes();
        $filePath = TPath::FromLib('data');
        $filePath .= '\testmailboxes.csv';
        $f = @fopen($filePath,"r");
        if ($f === false) {
            return;
        }

        while (!feof($f)) {
            $record = fgets($f);
            $record = trim($record);
            if (empty($record)) {
                continue;
            }
            $fields = explode(',',trim($record));
            $length = sizeof($fields);
            $code = $length < 1 ? ""  : str_replace('[comma]',',',$fields[0]);
            $name = $length < 2 ? ""  : str_replace('[comma]',',',$fields[1]);;
            $address = $length < 3 ? ""  : str_replace('[comma]',',',$fields[2]);
            $description = $length < 4 ? ""  : str_replace('[comma]',',',$fields[3]);

            $this->addMailbox( $code,$name,$address,$description);

        }
        fclose($f);
    }

    private function formatForSave($value) {
        if ($value === null)
            return '';
        $value = trim($value);
        return str_replace(',','[comma]',$value);
    }

    public function saveChanges() {
        $filePath = TPath::FromLib('data');
        $filePath .= '\testmailboxes.csv';
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $f = fopen($filePath,"w");
        $boxes = $this->getMailboxes();
        foreach($boxes as $box) {
            $code = $this->formatForSave($box->getMailboxCode());
            $name = $this->formatForSave($box->getName());
            $address = $this->formatForSave($box->getEmail());
            $description = $this->formatForSave($box->getDescription());

            fwrite($f,"$code,$name,$address,$description\n");
        }
        fclose($f);

    }

    private static function writeTestRecord($f,$code,$name,$address,$description)
    {
        fwrite($f,"$code,$name,$address,$description\n");
    }

    public static function setTestData() {
        TPostOffice::setInstance(null);
        $filePath = TPath::FromLib('data');
        $filePath .= '\testmailboxes.csv';
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $f = fopen($filePath,"w");

        self::writeTestRecord($f,'ADMIN','Terry SoRelle','tls@2quakers.net','Administrator address');
        self::writeTestRecord($f,'LIZ','Elizabeth Yeats','liz@2quakers.net','Liz address');

        fclose($f);

    }


}