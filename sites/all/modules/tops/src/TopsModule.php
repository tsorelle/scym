<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 4/10/2015
 * Time: 11:20 AM
 */

namespace Drupal\tops;

use Drupal\Core\Session\AccountInterface;
use \Tops\sys\TObjectContainer;
use \Drupal\tops\Controller\TopsController;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\JsonResponse;



class TopsModule {

    /**
     * @var TopsController
     */
    private static $topsController = null;
    private static $initialized = false;

    /**
     * @return TopsController|mixed
     */
    public static function GetTopsController()
    {
        if ( self::$topsController == null) {
            self::$topsController = TObjectContainer::Get('tops-controller');
        }
        return self::$topsController;
    }

    public static function Initialize() {
        if (!self::$initialized) {
            self::$initialized = true;
        }
    }

        /**
     *
     * Set TopsController fake for unit test
     *
     * @param $value
     */
    public static function SetTopsController($value) {
        self::$topsController = $value;
    }

    public static function ExecuteService($serviceCode = null, $parameter = null) {
        $req = Request::createFromGlobals();
        $controller = self::GetTopsController();
        if (empty($serviceCode)) {
            $response = $controller->executeService($req);
        }
        else {
            $response = $controller->getFromService($req,$serviceCode, $parameter);
        }

        $response->send();
        exit;

    }

    public static function GetHelpContent($path,$arg) {
        if ($path == 'admin/help#tops') {
            return '<p>Invokes a TOPS application for the corresponding KnockoutJS ViewModel.</p>';
        }
        return '';
    }

    /**
     * Return routing array for hook_menu()
     *
     * @return array
     */
    public static function GetRoutes() {
        $items = array();

        $items['tops/service'] = array(
            'page callback' => 'tops_service_execute',
            'access arguments' => array('execute service command'), // 'access content'),
            'type' => MENU_CALLBACK,
        );

        return $items;

    }

    /**
     * @param AccountInterface $drupalAccount
     */
    public static function UpdateUser(AccountInterface $drupalAccount)
    {

    }

    public static function test($serviceCode,$parameter) {
        $controller = self::GetTopsController();
        $response = $controller->test2($serviceCode,$parameter);
        $response->send();
        exit;

    }

    public static function GetPermissions() {
        return array(
          'execute service command' => array(
              'title' => 'Execute TOPS service',
              'description' => 'Post AJAX request to execute a service command.'
          ),
        );
    }



}