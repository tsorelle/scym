<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 7/10/2015
 * Time: 8:16 AM
 */
namespace migrateusersscript;


use Tops\cms\drupal7\DrupalAccountManager;

class program {
    public function run() {

        $qm = new  \Tops\db\TQueryManager();
        $newusers = $qm->executeStatement('SELECT * from migrate_users');
        $maxcount = 0;
        if (isset($_GET['maxcount'])) {
            $maxcount = $_GET['maxcount'];
        }
        $rowcount = 0;
        $errors = array();
        $added = array();
        while ($row = $newusers->fetch()) {
            // var_dump($row);
            $fullname = $this->getFullName($row);
            $result =  DrupalAccountManager::CreateAccount($row['username'],$row['email'],$row['password'],$fullname,$row['meeting']);
            if ($result->success) {
                $added[] = $result->message;
                $rowcount++;
                if ($maxcount > 0 && ($rowcount >= $maxcount)) {
                    break;
                }
            }
            else {
                $errors[] = $result->message;
                if ($result->exception) {
                    var_dump($result->exception);
                }
            }

        }
        print "Added: ".sizeof($added)."\n";
        print 'Skipped '.sizeof($errors)."\n";

        if (!empty($added)) {
            print "\nAdded accounts:\n";
            foreach($added as $account) {
                print "$account\n";
            }
        }

        if (!empty($errors)) {
            print "\nErrors:\n";
            foreach($errors as $error) {
                print "$error\n";
            }
        }
    }

    private function getFullName($row) {
        $result = $row['firstName'].' ';
        $middle = $row['middleName'];
        if (!empty($middle)) {
            $result .= $middle.' ';
        }
        return $result.$row['lastName'];
    }
}

$program = new program();
$program->run();





