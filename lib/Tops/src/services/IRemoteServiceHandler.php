<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/21/2015
 * Time: 4:50 AM
 */

namespace Tops\services;

use  Symfony\Component\HttpFoundation\Request;


/**
 * Interface IRemoteServiceHandler
 * @package Tops\services
 *
 * Handler for remote Peanut service requests.
 */
interface IRemoteServiceHandler {
    /**
     * @param Request $request
     * @return TServiceResponse
     */
    function handleRequest(Request $request);
}