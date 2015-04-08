<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/19/2015
 * Time: 6:31 AM
 */

namespace Tops\sys;
use Symfony\Component\Config\FileLocatorInterface;

/**
 * Class TConfigFileLocator
 * @package Tops\sys
 *
 * Used as a adaptor to supply file information from the TPath class
 * to Symfony 2 components such as configuration.
 *
 */
class TConfigFileLocator implements FileLocatorInterface {

    private $basePath;

    /**
     * Constructor.
     *
     * @param null $basePath
     * @internal param array|string $paths A path or an array of paths where to look for resources
     */
    public function __construct($basePath = null)
    {
        $this->basePath = $basePath;
    }

    /**
     * {@inheritdoc}
     */
    public function locate($name, $currentPath = null, $first = true)
    {
        if ('' == $name) {
            throw new \InvalidArgumentException('An empty file name is not valid to be located.');
        }

        if ($currentPath === null) {
            $currentPath = $this->basePath;
        }


        $filePath = ($currentPath === null) ?
            TPath::ConfigPath($name) :
            TPath::Append($currentPath,$name);

        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("The file '$filePath' does not exist");
        }
        return $filePath;
    }

}