<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/31/2014
 * Time: 7:56 AM
 */
require_once __DIR__ . '/../../core/vendor/autoload.php';
include (__DIR__."/../Tops/start/init.php");
\Tops\sys\TClassPath::Add('scym','App/src');

use Tops\sys\TConfig;

\Tops\test\TSmokeTest::Test();
\scym\db\TDBSmokeTest::Test();