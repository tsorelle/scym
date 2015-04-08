<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/22/2015
 * Time: 8:56 AM
 *
 * Deployment specific settings. Do not overwrite with dev version.
 */
$timezone = 'America/Chicago';
$includesPath = '';
date_default_timezone_set( $timezone ) ;
if (!empty($includesPath)) {
    ini_set('include_path', ini_get('include_path') . $includesPath);
}

unset($includesPath);
unset($timezone);