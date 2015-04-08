<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/26/2014
 * Time: 7:48 AM
 */

namespace Tops\sys;
// use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Used to disable error checking for functions which might run afowl of base_dir restriction.
 */
function ignoreFileErrors(){}  //  ignoreFileErrors

/**
 * Class TPath
 * @package Tops\sys
 *
 * Handles a variety of file and directory manipulation operations.
 */
class TPath
{
    private static $documentRoot = null;
    private static $libRoot = null;
    private static $configRoot = null;

    /**
     * Initialize key file locations
     *
     * @param null $documentRoot
     * @param null $configRoot
     * @throws \Exception
     */
    public static function Initialize($documentRoot = null, $configRoot = null)
    {
        if (self::$libRoot !== null)
            return; //already initialized.
        $libRoot = realpath(__DIR__ . '/../../..');
        if ($libRoot === false) {
            throw new \Exception("Unexpected location of tops library.");
        }
        self::$libRoot = $libRoot;

        if ($documentRoot === null) {
            $documentRoot = $libRoot . '/..';
        }
        $documentRoot = realpath($documentRoot);
        if ($documentRoot === false) {
            throw new \Exception("Unexpected location of library root.");
        }
        self::$documentRoot = $documentRoot;

        if ($configRoot == null) {
            $configRoot = $documentRoot . '/lib/App/config';
        }
        $configRoot = realpath($configRoot);
        if ($configRoot === false) {
            throw new \Exception("Unexpected location of config root");
        }
        self::$configRoot = $configRoot;
    }

    public static function GetConfigRoot()
    {
        self::Initialize();
        return self::$configRoot;
    }

    /**
     * Change directory separators based on OS
     *
     * @param $path
     * @return mixed
     */
    public static function Normalize($path)
    {
        if (DIRECTORY_SEPARATOR === '/')
            return str_replace("\\", '/', $path);
        return str_replace('/', "\\", $path);
    }

    private $path;

    public function __construct($path)
    {
        $this->path = self::FromRoot($path);
        if ($this->path === false)
            throw new \Exception("File path not found: $path");
    }

    public function __toString()
    {
        return $this->path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getFilePath($fileName) // , $throwException=false)
    {
        return self::Append($this->path,$fileName);
    }

    /**
     * Determine if path is valid
     *
     * @param $path
     * @return bool
     */
    public static function Exists($path) {
        set_error_handler('ignoreFileErrors');
        $result = file_exists($path);
        restore_error_handler();
        return $result;
    }

    /**
     * Concatenate to path strings resolving separators etc.
     * Normalizes both paths.
     *
     * @param $path1
     * @param $path2
     * @param bool $throwException
     * @return bool|string
     * @throws \Exception
     */
    public static function Join($path1, $path2, $throwException = false)
    {
        $path1 = realpath($path1);
        if ($path1 === false) {
            if ($throwException)
                throw new \Exception('Invalid path: '.$path1);
            return false;
        }
        return self::Append($path1,$path2);
    }

    /**
     * Append a second path to the first one.
     * Assumes that the first path has already been normalized
     *
     * @param $path1
     * @param $path2
     * @param bool $throwException
     * @return string
     * @throws \Exception
     */
    public static function Append($path1, $path2, $throwException=false) {

        $path2 = self::Normalize($path2);
        if (strpos($path2,DIRECTORY_SEPARATOR) !== 0) {
            $path1 = $path1.DIRECTORY_SEPARATOR.$path2;
        }
        else {
          $path1 = $path1.$path2;
        }
        $path1 = realpath($path1);

        if ($path1 === false && $throwException) {
            throw new \Exception('Invalid path: '.$path1);
        }
        return $path1;
    }

    /**
     * Return fully qualified path for file in the default configuration directory
     *
     * @param $fileName
     * @return string
     * @throws \Exception
     */
    public static function ConfigPath($fileName) {
        self::Initialize();
        return self::Append(self::$configRoot,$fileName);
    }

    /**
     * Return fully qualified path starting in the application root directory (typically site document root)
     *
     * @param string $path
     * @param bool $throwException
     * @return null|string
     * @throws \Exception
     */
    public static function FromRoot($path='',$throwException=false)
    {

        self::Initialize();
        if (empty($path))
            return self::$documentRoot;

        $result = realpath($path);

        if (strpos($result.DIRECTORY_SEPARATOR, self::$documentRoot.DIRECTORY_SEPARATOR === 0))
            return $result;

        $result = self::Append(self::$documentRoot,$path, $throwException);

        return $result;
    }

    /**
     * Return fully qualified path starting in the default library directory
     *
     * @param string $path
     * @param bool $throwException
     * @return null|string
     * @throws \Exception
     */
    public static function FromLib($path='',$throwException=false)
    {
        self::Initialize();
        if (empty($path))
            return self::$libRoot;

        $result = self::Append(self::$libRoot,$path, $throwException);

        return $result;
    }


}   // finish class TFilePath


