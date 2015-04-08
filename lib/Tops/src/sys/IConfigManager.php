<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/18/2015
 * Time: 7:58 AM
 */

namespace Tops\sys;


/**
 * Interface IConfigManager
 * @package Tops\sys
 */
interface IConfigManager  {
    /**
     * Get settings by config file and section
     *
     * @param $configName
     * @param string $subSection
     * @return IConfiguration
     */
    public function get($configName, $subSection = '');

    /**
     * Get settings from section corresponding to local environment name
     *
     * @param $configName
     * @param string $subSection
     * @return IConfiguration
     */
    public function getLocal($configName, $subSection = '');

    /**
     * Get name of local environment
     *
     * @return string
     */
    public function getEnvironment();
}