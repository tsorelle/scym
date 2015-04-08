<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/9/2015
 * Time: 1:15 PM
 */
namespace Tops\services;

/**
 * Interface IServiceFactory
 * @package Tops\services
 */
interface IServiceFactory {
    /**
     * Instantiate a Peanut service command by serviceId value.
     *
     * @param $serviceId
     * @return TServiceCommand
     */
    public function CreateService($serviceId);

}