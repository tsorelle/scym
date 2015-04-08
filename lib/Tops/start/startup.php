<?php
/******
/* Use this startup file when not invoking from Drupal
******/
// bootstrap Drupal
chdir($_SERVER['DOCUMENT_ROOT']); // change this to drupal location if not at root.
require_once './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

// continue startup - similar to tops_init()
echo "<div>Tops Initialzing</div>";
// get tops startup parameters
$configFile = realpath('tops/tops.ini');
if (empty($configFile)) {
    // default library paths and tracer flag
    $topslib = realpath('tops/tops_lib');
    $sitelib = realpath('tops/site_lib');
    $traceEnabled = false;
}
else {
    // load settings
    $startupSettings = parse_ini_file($configFile);

    // Get path for tops libraries
    $topslib = realpath(
        isset($startupSettings['topslib']) ? $startupSettings['topslib'] : 'tops/tops_lib');
    $sitelib = realpath(
        isset($startupSettings['sitelib']) ? $startupSettings['sitelib'] : 'tops/site_lib');

    // get tracer switch
    $traceEnabled = (!empty($startupSettings['trace']));
}

// Store library paths and update PHP includes path
require_once("$topslib/sys/TClassLib.php");
// TClassLib::Create($topslib, $sitelib);
TClassLib::Create();

// Set __autoload class search
require_once('tops_lib/sys/TClassPath.php');
//TClassPath::Add($topslib,'sys','db','model','view','ui','drupal/sys');
// TClassPath::Add($sitelib,'model','view','sys');

// Set __autoload class search
TClassPath::Add(TClassLib::GetTopsLib(),'sys','db','model','view','ui','drupal/sys');
TClassPath::Add(TClassLib::GetSiteLib(),'model','view','sys');

// enable tops error handling
require_once ('tops_lib/sys/errorHandling.php');

// start trace
if ($traceEnabled) {
    TTracer::On();
    TTracer::Trace("Started global trace");
    //TTracer::Trace("sitelib = $sitelib");
    //TTracer::Trace("topslib = $topslib");
    //TTracer::ShowArray($startupSettings);
}

global $user;
if (isset($user)) {
    TDrupalUser::SetCurrent($user);
}
else {
    TDrupalUser::SetCurrent();
}
