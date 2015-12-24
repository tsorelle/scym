<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/10/2015
 * Time: 6:24 AM
 */
date_default_timezone_set('America/Chicago');
require_once __DIR__."/TTestLoader.php";
// autoload tops and drupal core
$loader = require_once(__DIR__.'/../Tops/start/autoload.php');
\Tops\sys\TClassPath::Add('\Fakes','test/unit/fakes');


// autoload all extensions and set up drupal testing
// require_once(__DIR__.'/../../core/tests/bootstrap.php');

\Tops\test\TTestLoader::Create($loader,'tops');
// to autoload other Drupal extensions in selected tests:
// \Tops\test\TTestLoader::LoadModules("somemodule");
