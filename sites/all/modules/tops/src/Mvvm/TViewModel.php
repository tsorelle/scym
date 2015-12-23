<?php
/**
 * Created by PhpStorm.
 * User: tsorelle
 * Date: 3/3/14
 * Time: 4:25 AM
 */
namespace Drupal\tops\Mvvm;

use Drupal;
use Symfony\Component\HttpFoundation\Request;
use Tops\sys\TPath;
use Tops\sys\TPostOffice;

class TViewModel
{
    private static $vmPaths = null;
    private static $vmname = null;

    public static function isVmPage()
    {
        if (self::$vmname && array_key_exists(self::$vmname,self::$vmPaths)) {
            return true;
        }
        return false;
    }
    
    /**
     *
     * Extracts an alias from the request returns it if it is valid for a view model
     * Rules for ViewModel names exclude the names of first level Drupal and Tops directories
     * and they cannot have a file extension.
     *
     * @param Request $request
     * @return bool|null|string
     */
    public static function getNameFromRequest(Request $request)
    {
        $path = $request->getPathInfo();
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if (
            // exclude invalid or unknown path
            (!empty($path)) &&
            // exclude file or document requests
            empty($ext) &&
            // exclude posts and AJAX requests
            ($request->getMethod() == 'GET' && $request->getRequestFormat() == 'html')
        ) {
            $pathParts = explode('/', $path);
            $count = count($pathParts);
            if ($count < 2) {
                return null;
            }

            $name = $pathParts[1];
            $lowerName = strtolower($name); // for case insensitive compare
            // exclude Drupal and Tops root directories and standard Drupal root functions
            if ($lowerName == 'config' || $lowerName == 'admin' || $lowerName == 'user' ||
                $lowerName == 'sites' || $lowerName == 'misc' || $lowerName == 'assets' || $lowerName == 'lib' || $lowerName == 'core' ||
                    $lowerName == 'modules' || $lowerName == 'themes' || $lowerName == 'tops'
            ) {
                return null;
            }

            $arg = '';

            // if 'node' get arguments
            if ($name == 'node') {
                if ($count < 3 || !is_numeric($pathParts[2])) {
                    return null;
                }
                if ($count > 3) {
                    $arg = $pathParts[3];
                }
            } else {
                if ($count > 2) {
                    $arg = $pathParts[2];
                }
            }

            // eliminate standard form functions
            if ($arg == 'add' || $arg == 'edit') {
                return null;
            }

            // get node alias
            if ($name == 'node') {
                $aliasManager = self::getAliasManager();
                if (!$aliasManager) {
                    return null;
                }
                $name = $aliasManager->getAliasByPath(null); // $path);
                if (strstr('/node/', $name)) {
                    // must have alias
                    return null;
                }
                else {
                    return $name;
                }
            }
            if ($count <= 2) {
                return $name;
            }
            $path = $pathParts[1];
            for ($i = 2; $i < $count; $i++) {
                $path .= '/'.$pathParts[$i];
            }
            return $path;

            //return  $count > 2 ? $pathParts[1].'/'.$pathParts[2] : $name;
        }

        return null;
    }

    /**
     * @var Drupal\Core\Path\AliasManagerInterface
     */
    private static $aliasManager;

    /**
     * @return Drupal\Core\Path\AliasManagerInterface
     */
    private static function getAliasManager() {
        if (!self::$aliasManager) {
            self::$aliasManager = Drupal::service('path.alias_manager');
        }
        return self::$aliasManager;
    }


    public static function getVmPath() {
        $name = self::$vmname;
        if ($name && array_key_exists($name,self::$vmPaths)) {
            return self::$vmPaths[$name];
        }
        return null;
    }

    public static function mappingRequired($vmPath) {

    }

    public static function getVmDirectory() {
        // maybe replace with configuration some day
        return '/assets/js/Tops.App';
    }

    private static function getVMPathList() {
        if (self::$vmPaths === null) {
            self::$vmPaths = array();
            $configpath = TPath::ConfigPath('viewmodels.csv');
            $configFile = @fopen($configpath,'r');
            if ($configFile) {
                $vmDirectory = self::getVmDirectory();
                // $vmRootPath = \Tops\sys\TPath::FromRoot($vmDirectory);
                while(!feof($configFile)) {
                    $item = new \stdClass();
                    $parts =  explode(',',fgets($configFile));
                    $partsCount = sizeof($parts);
                    $item->requires = array();
                    if ($partsCount > 1) {
                        $key = trim($parts[0]);
                        $value = trim($parts[1]);
                        $item->path = $vmDirectory.'/'.$value.'.js';
                        $item->messagesComponent = true;
                        if ($partsCount > 2) {
                            for ($i = 2; $i < $partsCount; $i++) {
                                $requirement =  trim($parts[$i]);
                                if ($requirement == 'no-messages')  {
                                    $item->messagesComponent = false;
                                }
                                else {
                                    $item->requires[$i - 2] = trim($parts[$i]);
                                }
                            }
                        }
                        self::$vmPaths[$key] = $item;
                        // self::$vmPaths[$key] = $vmDirectory.'/'.$value.'.js';
                    }
                }
                fclose($configFile);
            }
        }
        return self::$vmPaths;
    }

    public static function Initialize(Request $request) {
        $name = self::getNameFromRequest($request);
        if ($name)
        {
            $key = strtolower($name);
            $vmPaths = self::getVMPathList();
            if (array_key_exists($key,$vmPaths)) {
                self::$vmname = $key;
            }
        }
        return false;
    }

    public static function RenderMessageElements() {
        if (self::getVmPath()) {
            return '<div id="service-messages-container"><service-messages></service-messages></div>';
        }
        return '';
    }

    public static function RenderStartScript() {
        $vmPath = self::getVmPath();
        if ($vmPath)
        {
            return
                "   ViewModel.init('/');\n".
                "   ko.applyBindings(ViewModel); \n";
        }
        return '';
    }
} 