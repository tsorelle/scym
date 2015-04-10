<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/8/2014
 * Time: 5:41 PM
 */

namespace Tops\sys;

/**
 * Based on PSR-4 example from FIG: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md
 * An example of a general-purpose implementation that includes the optional
 * functionality of allowing multiple base directories for a single namespace
 * prefix.
 *
 * Given a foo-bar package of classes in the file system at the following
 * paths ...
 *
 *     /path/to/packages/foo-bar/
 *         src/
 *             Baz.php             # Foo\Bar\Baz
 *             Qux/
 *                 Quux.php        # Foo\Bar\Qux\Quux
 *         tests/
 *             BazTest.php         # Foo\Bar\BazTest
 *             Qux/
 *                 QuuxTest.php    # Foo\Bar\Qux\QuuxTest
 *
 * ... add the path to the class files for the \Foo\Bar\ namespace prefix
 * as follows:
 *
 *      <?php
 *      // instantiate the loader
 *      $loader = new \Example\Psr4AutoloaderClass;
 *
 *      // register the autoloader
 *      $loader->register();
 *
 *      // register the base directories for the namespace prefix
 *      $loader->addNamespace('Foo\Bar', '/path/to/packages/foo-bar/src');
 *      $loader->addNamespace('Foo\Bar', '/path/to/packages/foo-bar/tests');
 *
 * The following line would cause the autoloader to attempt to load the
 * \Foo\Bar\Qux\Quux class from /path/to/packages/foo-bar/src/Qux/Quux.php:
 *
 *      <?php
 *      new \Foo\Bar\Qux\Quux;
 *
 * The following line would cause the autoloader to attempt to load the
 * \Foo\Bar\Qux\QuuxTest class from /path/to/packages/foo-bar/tests/Qux/QuuxTest.php:
 *
 *      <?php
 *      new \Foo\Bar\Qux\QuuxTest;
 */
class TClassPath
{
    /**
     * @var TClassPath
     */
    public static $instance;
    private $libPath;

    function __construct($libPath) {
        $this->libPath = $this->normalizePath($libPath,true);
    }


    /**
     * Normalize path and convert to Unix style.
     * Add terminiating seperator if indicated
     *
     * @param string $path path to fix
     * @param bool $addTerminator add trailing terminator
     * @return string
     */
    public function normalizePath($path,$addTerminator = true)
    {
        $result = trim($path);
        if ($result != '') {
            $result =  str_replace('\\','/',$result);
            if ($addTerminator) {
                return $result. '/';
            }
        }
        return $result;
    }

    public function joinPath($path1,$path2, $addTerminator = true ) {
        $path1 = $this->normalizePath($path1); // normailze and terminate
        return $this->appendPath($path1,$path2,$addTerminator);
    }

    public function appendPath($path1,$path2, $addTerminator = true ) {
        // assume path 1 normalized and terminated previously
        $path2 =  $this->normalizePath($path2, $addTerminator);
        return $path1.$path2;
    }


    public static function Create($libPath) {
        self::$instance = new TClassPath($libPath);
        self::$instance->register();
        self::$instance->addNamespace('Tops','Tops/src');
    }

    public static function AddExternal($prefix, $basePath, $subPath = NULL) {
        if ($subPath === NULL) {
            $subPath = $prefix;
        }
        self::$instance->addNamespace($prefix,$subPath,false,$basePath);

    }

    public static function Add($prefix, $subPath = NULL) {
        if ($subPath === NULL) {
            $subPath = $prefix;
        }
        self::$instance->addNamespace($prefix,$subPath);
    }

    /**
     * An associative array where the key is a namespace prefix and the value
     * is an array of base directories for classes in that namespace.
     *
     * @var array
     */
    protected $prefixes = array();

    /**
     * Register loader with SPL autoloader stack.
     *
     * @return void
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * Adds a base directory for a namespace prefix.
     *
     * @param string $prefix The namespace prefix.
     * @param string $subPath Concatenated to base directory for class files in the
     * namespace.
     * @param bool $prepend If true, prepend the base directory to the stack
     * instead of appending it; this causes it to be searched first rather
     * than last.
     * @return void
     */
    public function addNamespace($prefix, $subPath, $prepend = false, $basePath = null)
    {
        // normalize namespace prefix
        $prefix = trim($prefix, '\\') . '\\';

        if (empty($basePath)) {
            $basePath = $this->libPath;
        }
        else {
            $basePath = $this->normalizePath($basePath,true);
        }

        // normalize the base directory with a trailing separator
        $base_dir = $this->appendPath($basePath,$subPath, true);

        // initialize the namespace prefix array
        if (isset($this->prefixes[$prefix]) === false) {
            $this->prefixes[$prefix] = array();
        }

        // retain the base directory for the namespace prefix
        if ($prepend) {
            array_unshift($this->prefixes[$prefix], $base_dir);
        } else {
            array_push($this->prefixes[$prefix], $base_dir);
        }
    }

    /**
     * Loads the class file for a given class name.
     *
     * @param string $class The fully-qualified class name.
     * @return mixed The mapped file name on success, or boolean false on
     * failure.
     */
    public function loadClass($class)
    {
        // the current namespace prefix
        $prefix = $class;

        // work backwards through the namespace names of the fully-qualified
        // class name to find a mapped file name
        while (false !== $pos = strrpos($prefix, '\\')) {

            // retain the trailing namespace separator in the prefix
            $prefix = substr($class, 0, $pos + 1);

            // the rest is the relative class name
            $relative_class = substr($class, $pos + 1);

            // try to load a mapped file for the prefix and relative class
            $mapped_file = $this->loadMappedFile($prefix, $relative_class);
            if ($mapped_file) {
                return $mapped_file;
            }

            // remove the trailing namespace separator for the next iteration
            // of strrpos()
            $prefix = rtrim($prefix, '\\');
        }

        // never found a mapped file
        return false;
    }

    /**
     * Load the mapped file for a namespace prefix and relative class.
     *
     * @param string $prefix The namespace prefix.
     * @param string $relative_class The relative class name.
     * @return mixed Boolean false if no mapped file can be loaded, or the
     * name of the mapped file that was loaded.
     */
    protected function loadMappedFile($prefix, $relative_class)
    {
        // are there any base directories for this namespace prefix?
        if (isset($this->prefixes[$prefix]) === false) {
            return false;
        }

        // look through base directories for this namespace prefix
        foreach ($this->prefixes[$prefix] as $base_dir) {

            // replace the namespace prefix with the base directory,
            // replace namespace separators with directory separators
            // in the relative class name, append with .php
            $file = $base_dir
                . str_replace('\\', '/', $relative_class)
                . '.php';

            // if the mapped file exists, require it
            if ($this->requireFile($file)) {
                // yes, we're done
                return $file;
            }
        }

        // never found it
        return false;
    }

    /**
     * If a file exists, require it from the file system.
     *
     * @param string $file The file to require.
     * @return bool True if the file exists, false if not.
     */
    protected function requireFile($file)
    {
        // D:\Projects\websites\tops\tops.root\lib\Tops\src\test\TDBSmokeTest.php
        if (file_exists($file)) {
            /** @noinspection PhpIncludeInspection */
            require $file;
            return true;
        }
        return false;
    }
}