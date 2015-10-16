<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 4/17/2015
 * Time: 5:44 AM
 *
 * Run this script to prepare for deployment.
 * Minifies LESS generated style.css to scym-theme.min.css
 *
 * Requires Java installed: https://www.java.com
 *
 */

$themename='scymtheme';
$rootpath=realpath(__DIR__.'/../..');
$themeroot=$rootpath.'/sites/all/themes/'.$themename.'/';
$tmppath=realpath($rootpath.'/../tmp');
$yuicompressor=__DIR__.'/yuicompressor.jar';

copy ($themeroot.'less/style.css', $tmppath.'/temp.css');
$content = file_get_contents($themeroot.'less/animate.css');
file_put_contents($tmppath.'/temp.css', $content, FILE_APPEND);

// $minifyCss = "java -jar $yuicompressor $themeroot".'less/style.css  > '.$themeroot.'assets/css/scym-theme.min.css';
$minifyCss = "java -jar $yuicompressor $tmppath/temp.css > ".$themeroot.'assets/css/scym-theme.min.css';

echo 'Compressing CSS file...';
exec($minifyCss);

echo "\n\nDone\n";