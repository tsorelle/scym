<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 3/15/2015
 * Time: 6:09 PM
 *
 * Autoload techniques:
 * To autoload tops and drupal core:
 *    require __DIR__.'/../Tops/start/autoload.php';
 *
 * To autoload tops, drupal and selected extensions such as tops module:
 *   $loader = require __DIR__.'/../Tops/start/autoload.php';
 *   Tops\test\TTestLoader::Create($loader,"tops");
 *
 * To autoload tops, drupal core and all Drupal Extensions:
 *    require_once __DIR__.'/../Tops/start/drupalAutoload.php';
 */

$x = array(1);

if ($x[1]) {
    print 'ok'; //
}
else print 'foo';

print done.
    exit;



require_once __DIR__."/TTestLoader.php";

print "starting\n";
/*
// autoload tops, drupal core and tops module
$loader = require __DIR__.'/../Tops/start/autoload.php';
Tops\test\TTestLoader::Create($loader,"tops");

use Tops\sys\TSession;
TSession::Initialize();
$t = TSession::GetSecurityToken();
*/
$path = false; // 'foo/bar/readme';
$ext = pathinfo($path, PATHINFO_EXTENSION);
print "\n$ext\n";
if  (!empty($ext))
// (!TSession::AuthenitcateSecurityToken($t))
{

    print "Failed!\n";

}
else print "ok\n";
/*
for ($i=0; $i<20; $i++) {
    print TSession::createToken()."\n";
}
*/

/*
print "loaded\n\n";
if (class_exists('Drupal\tops\Controller\TopsController')) {
    print "found it\n";
    if (class_exists('\Drupal\tops\sys\TDrupalTest')) {
        print "hooray\n";
        // \Drupal\tops\sys\TDrupalTest::speak();

    }
    else print "hiss!boo!\n";
}
else {
    print "foo!\n";
}
*/
//

// \Drupal\tops\sys\TDrupalTest::speak();