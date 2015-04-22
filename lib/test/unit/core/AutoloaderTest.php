<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/10/2015
 * Time: 7:14 AM
 */

class AutoloaderTest extends PHPUnit_Framework_TestCase {
    /**
     * Check to see if class loaded by autoloader.
     */
    public function testFindTopsClass() {
        $this->findClass('\Drupal\tops\Controller\TopsController');
        $this->findClass('\Symfony\Component\Config\Resource\DirectoryResource');
        $this->findClass('\Doctrine\DBAL\DriverManager');
        $this->findClass('\Symfony\Component\Config\Loader\FileLoader');
        $this->findClass('\Tops\db\TEntityManagers');
        $this->findClass('\Tops\test\TSmokeTest');
        $this->findClass('\App\test\TAppSmokeTest');
        $this->findClass('\Doctrine\ORM\NativeQuery');
        $this->findClass('\Doctrine\ORM\EntityManager');
    }

    private function findClass($className) {
        $this->assertTrue(class_exists($className),"Class not found '$className'");
    }
}
