<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/19/2015
 * Time: 3:47 PM
 */

namespace Tops\sys;


/**
 * Class TMemoryConfigManager
 * @package Tops\sys
 *
 * A class factory for TConfig with a memory config data source
 * Used for unit testing.
 */
class TMemoryConfigManager extends TConfigSection implements IConfigManager {
    /**
     * Get settings by config file and section
     *
     * @param $configName
     * @param string $subSection
     * @return IConfiguration
     */
    public function get($configName, $subSection = '')
    {
        $result = $this->GetSection($configName);
        if (empty($result)) {
            $result = $this;
        }
        if ($subSection) {
            return $result->GetSection($subSection);
        }
        return $result;
    }

    /**
     * Get settings from section corresponding to local environment name
     *
     * @param $configName
     * @param string $subSection
     * @return IConfiguration
     */
    public function getLocal($configName, $subSection = '')
    {
        $environment = $this->getEnvironment();
        $result = $this->GetSection($environment);
        if (empty($result)) {
            $result = $this;
        }

        if ($subSection) {
            $result = $result->GetSection($subSection);
        }

        return $result;

    }

    /**
     * Get name of local environment
     *
     * @return string
     */
    public function getEnvironment()
    {
        $result = $this->Value("environment");
        return empty($result) ? 'development' : $result;
    }

}