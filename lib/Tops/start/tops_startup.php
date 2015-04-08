<?php
/******
/* Include this startup file with Drupal index.php or tops\startup.php
******/
date_default_timezone_set( 'America/Chicago' ) ;

// Define root path for tops libraries and sets include paths.
$DOCUMENT_ROOT=str_replace("\\",'/',$_SERVER['DOCUMENT_ROOT']);
require_once("$DOCUMENT_ROOT/tops/tops_lib/sys/TClassLib.php");
TClassLib::Create();

// Set __autoload class search
require_once("tops_lib/sys/TClassPath.php");
TClassPath::Add(TClassLib::GetTopsLib(),'sys','db','model','view','ui','drupal/sys');
TClassPath::Add(TClassLib::GetSiteLib(),'model','view','sys');

// add additional include directories
$includesPath = '';
if (!empty($includesPath))
    ini_set('include_path', ini_get('include_path').$includesPath);
unset($includesPath);

// Load error handling
require_once("tops_lib/sys/errorHandling.php");



