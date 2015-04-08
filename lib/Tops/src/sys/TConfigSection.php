<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/30/2015
 * Time: 2:47 PM
 */

namespace Tops\sys;


class TConfigSection implements IConfiguration
{
    private $configData;

    public function __construct($configData = null)
    {
        if ($configData !== null) {
            $this->configData = $configData;
        } else {
            $this->configData = array();
        }
    }

    /**
     * @return array
     */
    protected function getConfigData()
    {
        return $this->configData;
    }

    /**
     * @param array $configData
     */
    protected function setConfigData($configData)
    {
        $this->configData = $configData;
    }

    /**
     * @param array $configData
     * @param string $subSection
     *
     * Set config data source from a memory array.
     * Typically used in unit testing.
     */
    public function setConfig(Array $configData, $subSection = '')
    {
        if (!empty($subSection)) {
            $configData = $this->getValue($configData[$subSection]);
        }
        $this->configData = $configData;
    }


    /**
     * @param string $sectionPath
     * @param null $default
     * @return null
     */
    public function Value($sectionPath = '', $default = null)
    {
        $result = $this->getValue($sectionPath, $this->configData);
        if ($result === null) {
            return $default;
        }

        return $result;
    }

    /**
     * @param $sectionPath
     * @return array|TConfigSection
     */
    public function GetSection($sectionPath)
    {
        $result = $this->getValue($sectionPath, $this->configData);
        if ($result === null) {
            return new TConfigSection();
        }

        return new TConfigSection($result);
    }

    /**
     * @param string $sectionPath
     * @param $result
     * @return mixed
     */
    protected function getValue($sectionPath = '', $result)
    {
        if (!empty($sectionPath)) {
            $keys = explode('/', $sectionPath);
            $count = count($keys);
            for ($i = 0; $i < $count; $i++) {
                $result = $this->getConfig($result, $keys[$i]);
            }
        }

        return $result;
    }

    protected function getConfig($source, $keyName = '')
    {
        $length = is_array($source) ? count($source) : 0;
        if ($length > 0) {
            if (empty($keyName)) {
                return $source[0];
            }
            if (array_key_exists($keyName, $source)) {
                return $source[$keyName];
            }
        }

        return null;
    }

    public function clearConfigData()
    {
        $this->configData = array();
    }

    public function CrossReferenceSection($referencePath)
    {
        $realKey = $this->Value($referencePath);
        if (!$realKey) {
            return null;
        }

        return $this->Value($realKey);
    }

    /**
     * @param $sectionPath
     * @param bool $default
     * @return boolean
     */
    public function IsTrue($sectionPath, $default = false)
    {
        $value = $this->Value($sectionPath);
        if ($value === null) {
            return $default;
        }

        return !empty($value);
    }
}