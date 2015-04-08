<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/26/2014
 * Time: 11:38 AM
 */

namespace Tops\sys;
use Symfony\Component\Yaml\Parser;


class TConfig {
    private $configData;

    public function __construct($fileName,$subSection = '') {
        $filePath = TPath::ConfigPath($fileName.'.yml');
        $yaml = new Parser();
        $raw = file_get_contents($filePath);
        $section = $yaml->parse($raw);
        if (!empty($subSection)) {
            $section = $this->getValue($subSection,$section);
        }
        $this->configData = $section;
    }

    public function Value($sectionPath='') {
        return $this->getValue($sectionPath,$this->configData);
    }

    private function getValue($sectionPath='',$result) {
        if (!empty($sectionPath)) {
            $keys = explode('/', $sectionPath);
            $count = count($keys);
            for ($i = 0; $i < $count; $i++) {
                $result = $this->getConfig($result,$keys[$i]);
            }
        }
        return $result;
    }

    public function CrossReferenceSection($referencePath) {
        $realkey = $this->Value($referencePath);
        if (!$realkey)
            return null;
        return $this->Value($realkey);
    }

    private function getConfig($source, $keyName='')
    {
        $length = count($source);
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

}