<?php
use Tops\sys\TContentManager;
use Tops\sys\TObjectContainer;

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/24/2015
 * Time: 7:13 AM
 */


class TContentManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Really just testing the DI
     */
    public function testGetText()
    {

        TObjectContainer::Clear();
        TObjectContainer::Register('contentManager','Fakes\FakeContentManager');

        // NodeManager won't work without Drupal bootstrap
        // \Tops\sys\TObjectContainer::Register('contentManager','Drupal\tops\content\NodeManager');

        $expected = 'test: processed';
        $actual = TContentManager::GetText('test');
        $this->assertEquals($expected,$actual);
    }

}
