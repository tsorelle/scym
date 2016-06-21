<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 7/7/2015
 * Time: 6:27 AM
 */

namespace App\sys;
use Tops\db\TQueryManager;
use Tops\sys\IMessageContainer;
use Tops\sys\TPath;

class ScymDocumentImporter {
    /**
     * @var TQueryManager
     */
    private $qm;

    /**
     * @var TQueryManager
     */
    private $drupalqm;

    /**
     * @var string
     */
    private $docPath;

    /**
     * @var array
     */
    private $vocabularies;

    /**
     * @var string
     */
    private $privatePath;

    /**
     * @var IMessageContainer
     */
    private $messages;


    private $access = null;

    function __construct(IMessageContainer $messsages) {
        $this->messages = $messsages;
        $this->qm = new TQueryManager();
        $this->drupalqm = new TQueryManager('drupal');
        $vocabularies =  taxonomy_vocabulary_get_names();
        $this->vocabularies = array();
        foreach ($vocabularies as $v) {
            $this->vocabularies[$v->name] = $v->machine_name;
        }
    }

    public function getDocPath() {
        return $this->docPath;
    }
    
    private function setAccess($access) {
        if (empty($access)) {
            $access ='private';
        }
        if ($this->access != $access) {
            if ($access == 'private') {
                $basePath = variable_get('file_private_path');
            }
            else if ($access == 'public') {
                $basePath = variable_get('file_public_path', conf_path() . '/files');
            }
            else {
                throw new \Exception("Invalid access type '$access'");
            }
            $basePath =  TPath::FromRoot($basePath);
            if ($basePath == null) {
                throw new \Exception('Document base path not found.');
            }

            $this->docPath = $basePath.'/documents/';
            $this->access = $access;
            return $this->access.': '.$this->docPath;
        }
    }


    private function insertIntoFileTable($fileInfo) {
        $fileManagedInsertValues = array(
            'uid' => 1,
            'filename' => $fileInfo->name,
            'uri' => $fileInfo->uri,
            'filemime' => 'application/pdf',
            'filesize' => $fileInfo->size,
            'status' => 1,
            'timestamp' => $fileInfo->time,
        );
        return $this->drupalqm->insert('file_managed',$fileManagedInsertValues);
    }

    private function isPdf($fileName) {
        $parts = explode('.',$fileName);
        $last = sizeof($parts)-1;
        return (strtolower( $parts[$last]) == 'pdf');
    }

    private function getFileInfo($fileName) {
        $filePath = $this->docPath.$fileName;
        $fileStat = stat($filePath);
        if ($fileStat !== false) {
            $result = new \stdClass();
            $result->name = $fileName;
            $result->location = $filePath;
            $result->uri =  $this->access.'://documents/'.$fileName;
            $result->size = $fileStat['size'];
            $result->time =  $fileStat['mtime'];
            return $result;
        }
        else {
            $this->messages->AddErrorMessage("File not found: $filePath");

        }
        return false;
    }

    private function getDocType($docType,$title)
    {
        if (!empty($docType)) {
            return $docType;
        }
        if (stristr( $title,'report')) {
            return 'Report';
        }
        return '';
    }

    private function getDocumentStatus($title) {
        if (stristr($title,'newsletter')) {
            return 'Informal';
        }
        return 'Approved';
    }

    private function dateToArray($value)
    {
        $time = strtotime($value);
        if ($time === false) {
            $time = strtotime(date('Y-m-d'));
        }
        $dateValue = array(
            'value' => date('Y-m-d',$time).' 00:00:00',
            'timezone' => 'America/Chicago',
            'timezone_db' => 'America/Chicago',
            'date_type' => 'datetime'
        );
        return $this->toFieldSpec($dateValue);
    }


    private function toFieldSpec($value, $key = null)
    {
        if ($key) {
            $value = Array($key => $value);
        }
        $result = Array(
            'und' => array(
                0 => $value
            )
        );
        return $result;

    }

    private function getTaxonomyTerm($vocabulary,$termName) {
        if (empty($termName)) {
            return null;
        }
        $terms = taxonomy_get_term_by_name($termName,$this->vocabularies[$vocabulary]);
        if (empty($terms)) {
            return null;
        }
        $term = array_values($terms)[0];
        return $term;

    }
    private function taxonomyField($vocabulary,$termName) {
        $term = $this->getTaxonomyTerm($vocabulary,$termName);
        if (empty($term)) {
            return null;
        }
        return $this->toFieldSpec($term->tid,'tid');
    }

    private function createNode($fileInfo,$access='public') {
        $node = new \stdClass();
        $node->fid = $fileInfo->fid;
        $node->type = 'documents';
        node_object_prepare($node);
        $bodyText = "Document: $fileInfo->name";
        $node->title    = $fileInfo->title;
        $node->language = LANGUAGE_NONE;
        $fileParts = explode('.',$fileInfo->name);

        $node->body[$node->language][0]['value']   = $bodyText;
        $node->body[$node->language][0]['summary'] = text_summary($bodyText);
        $node->body[$node->language][0]['format']  = 'filtered_html';
        $path = 'documents/'.str_replace(' ','-',$fileParts[0]);
        $node->path = array('alias' => $path);
        $now = time();
        $fileSpec =
            Array(
                'fid' => $fileInfo->fid,
                'uid' => 1,
                'filename' => $fileInfo->name,
                'uri' => $fileInfo->uri,
                'filemime' => 'application/pdf',
                'filesize' => $fileInfo->size,
                'status' => 1,
                'timestamp' => $fileInfo->time,
                'rdf_mapping' => Array(),
                'display' => 1,
                // 'description' => null
            );
        $node->uid = 1;
        $node->title = $fileInfo->title;
        $node->language = 'und';
        $node->created = $now;
        $node->changed = $now;
        $node->tnid = 0;
        $node->translate = 0;
        $node->field_document_status = $this->toFieldSpec($fileInfo->docStatus,'value');
        if ($access == 'private') {
            $node->field_file_private_document_file = $this->toFieldSpec($fileSpec);
        }
        else {
            $node->field_document_file = $this->toFieldSpec($fileSpec);
        }
        $node->field_document_type = $this->toFieldSpec($fileInfo->docType,'value');
        $node->field_document_publication_date = $this->dateToArray($fileInfo->published);
        $field = $this->taxonomyField('Annual session years',$fileInfo->ymYear);
        if (!empty($field)) {
            $node->field_year_annual_session = $field;
        }
        $field = $this->taxonomyField('Committees',$fileInfo->committee);
        if (!empty($field)) {
            $node->field_committee = $field;
        }
        $field = $this->taxonomyField('Session types',$fileInfo->sessionType);
        if (!empty($field)) {
            $node->field_session_type = $field;
        }

        node_save($node);
        return $node->nid;
    }

    public function addDocument(\stdClass $doc)
    {

        $access = isset($doc->access) ? $doc->access : 'private';

        if (!$doc->filename) {
            $this->messages->AddErrorMessage("Document file name is required.");
            return 0;
        }
        if (!$this->isPdf($doc->filename)) {
            $this->messages->AddErrorMessage("Document must be in PDF format.");
            return 0;
        }




        $previouslyAdded = $this->drupalqm->executeScaler('count(*)', 'file_managed', 'filename=?', $doc->filename);
        if ($previouslyAdded) {
            $this->messages->AddErrorMessage("File '$doc->filename' already added.");
            return 0;
        }


        $this->setAccess($access);

        $fileInfo = $this->getFileInfo($doc->filename);
        if ($fileInfo === false) {
            return 0;
        }
        
        $fileInfo->fid = $this->insertIntoFileTable($fileInfo);

        // $fileInfo->fid = 99;
        $fileInfo->title = $doc->title;

        $fileInfo->published = empty($doc->datePublished) ? date('Y-m-d') : $doc->datePublished;

        if (!empty($doc->keywords)) {
            $fileInfo->keywords = $doc->keywords;
        }


        $docType = $this->getDocType(empty($doc->docType) ? '' : $doc->docType, $fileInfo->title);

        if (!empty($docType)) {
            $fileInfo->docType = $docType;
        }

        $fileInfo->docStatus = empty($doc->docStatus) ? 'Informal' : $doc->docStatus;

        if (!empty($doc->ymYear)) {
            $fileInfo->ymYear = $doc->ymYear;
        }

        if (!empty($doc->committee)) {
            $fileInfo->committee = $doc->committee;
        }

        if (!empty($doc->sessionType)) {
            $fileInfo->sessionType = $doc->sessionType;
        }

        // create node
        $nid = $this->createNode($fileInfo,$access);

        $this->messages->AddInfoMessage("Successfully registered '$doc->filename'");
        return $nid;
    }
}