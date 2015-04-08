<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/6/2015
 * Time: 8:11 AM
 */
namespace Tops\sys;

interface IExceptionHandler
{
    function handleException(\Exception $ex, $policyName = null);
}