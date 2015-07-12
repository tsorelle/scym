<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 6/23/2015
 * Time: 1:51 PM
 */

try {
    $qm = new  \Tops\db\TQueryManager();
    $docs = $qm->executeStatement('SELECT * from documents_migrate WHERE fileType=?','PDF');
    print "Adding documents ... \n";
    $messages = new \Tops\sys\TMemoryMessages();
    $importer = new \App\sys\ScymDocumentImporter($messages);
    $count = 0;
    $errorCount = 0;

    while ($row = $docs->fetch()) {
        $dto = new \stdClass();
        $dto->filename = $row['fileName'];
        $dto->title = $row['title'];
        $dto->datePublished = $row['datePublished'];
        $dto->keywords = $row['keywords'];
        $dto->docType = $row['docType'];
        $dto->docStatus = $row['docStatus'];
        $dto->ymYear = $row['ymyear'];
        $dto->committee = $row['committee'];
        $dto->sessionType = $row['sessiontype'];

        $nid = $importer->addDocument($dto);
        if ($nid == 0) {
            $errorCount++;
        }
        else {
            $count++;
        }
    }

    $results = $messages->getMessages();
    foreach ($results as $message) {
        print $message."\n";
    }

    print "\nAdded $count documents.\n";
    print "$errorCount documents skipped due to errors.\n";
}
catch(\Exception $ex) {
    print $ex->getMessage();
}

print "\ndone";

print '</pre>';
