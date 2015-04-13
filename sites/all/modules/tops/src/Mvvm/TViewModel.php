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

class TViewModel
{
    private static $vmPaths = array();
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
                if (!is_numeric($pathParts[2])) {
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
                // $name = $name . '/' . $pathParts[2];
                $aliasManager = self::getAliasManager();
                if (!$aliasManager) {
                    return null;
                }
                $name = $aliasManager->getAliasByPath($path);
                if (!strstr('/', $name)) {
                    return $name;
                }
                return null;
            }
            return $name;
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

    public static function Initialize(Request $request) {
        $name = self::getNameFromRequest($request);
        if ($name)
        {
            $vmPath = $vmPath = "assets/js/Tops.App/$name".'ViewModel.js';
            $vmLocation = TPath::FromRoot($vmPath);
            if (!empty($vmLocation)) {
                self::$vmPaths[$name] = $vmPath;
                self::$vmname = $name;
                return true;
            }
            else if (array_key_exists($name,self::$vmPaths)) {
                unset(self::$vmPaths[$name]);
            }
        }
        return false;
    }

    public static function RenderMessageElements() {
        if (self::getVmPath()) {
            return '<messages-component></messages-component>';
        }
        return '';
    }

    public static function RenderStartScript() {
        $vmPath = self::getVmPath();
        if ($vmPath)
        {
            return


               //  '<script src="'.$vmPath.'"'."></script>\n".
                // "<script>\n".
                "   ViewModel.init('/');\n".
                "   ko.applyBindings(ViewModel); // \n"
                // ."</script>\n"
                ;
        }
        return '';
    }
} 