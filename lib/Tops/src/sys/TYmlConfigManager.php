<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/18/2015
 * Time: 8:06 AM
 */

namespace Tops\sys;
use Symfony\Component\Yaml\Parser;

/**
 * Class TYmlConfigManager
 * @package Tops\sys
 * Class factory for Configuration objects using YML configuration files
 */
class TYmlConfigManager implements IConfigManager {

    private static $environment;
    /**
     * @var TConfigSection[]
     */
    private static $cache;

    /**
     * @param $fileName
     * @return TConfigSection
     */
    private  static function getFile($fileName) {
        if (isset(self::$cache)) {
            if (array_key_exists($fileName,self::$cache)) {
                return self::$cache[$fileName];
            }
        }
        else {
            self::$cache = array();
        }
        $filePath = TPath::ConfigPath($fileName.'.yml');
        $yaml = new Parser();
        $raw = file_get_contents($filePath);
        $contents = $yaml->parse($raw);
        $section = new TConfigSection($contents);
        self::$cache[$fileName] = $section;
        return $section;
    }


    /**
     * @inheritdoc
     */
    public function get($configName, $subSection = '')
    {
        $section = self::getFile($configName);
        if (!empty($subSection)) {
            $section = $section->GetSection($subSection);
        }
        return $section;
    }

    /**
     * @inheritdoc
     */
    public function getLocal($configName, $subSection = '') {
        $environment = $this->getEnvironment();
        return $this->get($configName.'-'.$environment,$subSection);
    }

    /**
     * @inheritdoc
     */
    public function getEnvironment()
    {
        if (!isset(self::$environment)) {
            $siteConfig = $this->get("sitesettings");
            self::$environment = $siteConfig->Value("environment");
            if (self::$environment == null) {
                throw new \Exception("No tops.yml environment setting found.");
            }
        }

        return self::$environment;

    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function Value($key, $default = null)
    {
        // TODO: Implement Value() method.
    }

    /**
     * @param $sectionPath
     * @return bool
     * @internal param bool $default
     */
    public function IsTrue($sectionPath)
    {
        // TODO: Implement IsTrue() method.
    }

    /**
     * @param $sectionPath
     * @return IConfiguration
     */
    public function GetSection($sectionPath)
    {
        // TODO: Implement GetSection() method.
    }
}