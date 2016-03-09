<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/24/2015
 * Time: 8:07 AM
 */

namespace Tops\sys;


class TContentManager
{
    /**
     * @var IContentManager
     */
    private static $instance;
    private static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = TObjectContainer::Get('contentManager');
        }
        return self::$instance;
    }

    public static function GetText($pathAlias)
    {
        $instance = self::getInstance();
        return $instance->getText($pathAlias);
    }

}