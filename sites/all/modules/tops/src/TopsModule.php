<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 4/10/2015
 * Time: 11:20 AM
 */

namespace Drupal\tops;

use Drupal\Core\Session\AccountInterface;
use Drupal\tops\Mvvm\TViewModel;
use \Tops\sys\TObjectContainer;
use \Drupal\tops\Controller\TopsController;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\JsonResponse;
use Tops\sys\TSession;
use Tops\sys\TTracer;
use Tops\cache\TSessionCache;


class TopsModule {

    /**
     * @var TopsController
     */
    private static $topsController = null;
    private static $initialized = false;
    private static $privateContentTermId;

    /**
     * @var TSessionCache
     */
    private static $sessionCache;

    /**
     * @return TSessionCache
     */
    private static function getSessionCache()
    {
        if (!isset(self::$sessionCache)) {
            self::$sessionCache = new TSessionCache();
        }
        return self::$sessionCache;

    }

    private static function getPrivateContentTermId()
    {
        $cache = self::getSessionCache();
        $tid = $cache->Get('termid.privatecontent');
        if ($tid === null) {
            $terms = taxonomy_get_term_by_name('Private', "content_access");
            if (!empty($terms)) {
                // taxonomy_get_term_by_name returns an associative array keyed by termid.
                // warning, this may be deprecated in Drupal 8
                $tid = array_keys($terms)[0];

                // $cache->Set("termid.privatecontent",$tid,120);
            }
        }

        return $tid;
    }

    public static function TracerOn($username = 'admin', $traceId='default') {
        if (\Drupal::currentUser()->getUsername() == $username) {
            TTracer::On($traceId);
        }
    }

    private static function userAuthenticated()
    {
        $user = \Drupal::currentUser();
        if ($user) {
            return $user->isAuthenticated();
        }
        return false;
    }

    private static function userAnonymous()
    {
        $user = \Drupal::currentUser();
        if ($user) {
            return $user->isAnonymous();
        }
        return false;
    }
    public static function hasTaxonomyTerm($node,$taxonomyFieldName,$termName)
    {
        $terms = field_view_field('node', $node, $taxonomyFieldName);
        if (isset($terms['#items'])) {
            foreach ($terms['#items'] as $item) {
                if (isset($item['taxonomy_term'])) {
                    $term = $item['taxonomy_term'];
                    if (($term->name == $termName)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    public static function checkContentAccess($node) {

       if (self::userAnonymous() && $node !== null && isset($node->type)) {
           $accessFieldName = 'field_' . $node->type . '_access';
           if (property_exists($node,$accessFieldName)) {
               if (self::hasTaxonomyTerm($node, $accessFieldName, 'Private')) {
                   drupal_set_message(t('You must sign in to view this content.'), 'error');
                   drupal_goto('/');
               }
           }
       }
    }

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
       //  TTracer::Trace('Start TopsModule::Initialize');

        if (!self::$initialized) {
            self::$initialized = true;
            $req = Request::createFromGlobals();
            TViewModel::Initialize($req);
            TSession::Initialize();
        }
        // TTracer::Trace('End TopsModule::Initialize');
    }

    public static function PreprocessHtml(&$variables) {
        $vmPath = TViewModel::getVmPath();
        $hasVm = (!empty($vmPath));
        $variables['peanut_viewmodel'] = $hasVm;
        $variables['peanut_viewmodel_src'] = $vmPath;
        // $variables['tops_js_debug'] =  \Tops\sys\TTracer::JsDebuggingOn();

        if ($hasVm) {
            $initJs =  "ViewModel.init('//', function() {  ko.applyBindings(ViewModel); });";

            drupal_add_library('tops','peanut.app');
            drupal_add_js($vmPath,array('group'=>'JS_THEME', 'scope'=>'footer'));
            drupal_add_js($initJs,array('group'=>'JS_THEME','type'=>'inline', 'scope'=>'footer'));
        }
    }
/*
    public static function addPeanutScripts() {

        $vmPath = TViewModel::getVmPath();
        $hasVm = (!empty($vmPath));
        if ($hasVm) {
            $initJs =  "ViewModel.init('//', function() {  ko.applyBindings(ViewModel); });";

            drupal_add_library('tops','peanut.app');
            drupal_add_js($vmPath,array('group'=>'JS_THEME', 'scope'=>'footer'));
            drupal_add_js($initJs,array('group'=>'JS_THEME','type'=>'inline', 'scope'=>'footer'));

        }
    }
*/


    public static function GetLibraries() {

        $libraries['knockoutjs'] = array(
            'title' => 'Knockout JS',
            'website' => 'http://cdnjs.cloudflare.com',
            'version' => '3.3.0',
            'js' => array(
                'http://cdnjs.cloudflare.com/ajax/libs/knockout/3.3.0/knockout-min.js' => array('type'=>'external', 'group' => JS_LIBRARY,  'weight' => '1')
            ),
        );

        $libraries['peanut'] = array(
            'title' => 'Peanut Service Library',
            'version' => '1.0',
            'js' => array(
                'assets/js/Tops.Peanut/Peanut.js' => array('group' => JS_LIBRARY, 'weight' => '2'),
            ),
            'dependencies' => array(
                array('system', 'jquery'),
                array('tops','knockoutjs'),
                // we also need bootstrap but will rely on bootstrap module for that
            ),
        );

        $libraries['underscorejs'] = array(
            'title' => 'Underscore JS',
            'website' => 'http://cdnjs.cloudflare.com',
            'version' => '1.8.3',
            'js' => array(
                'http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js' => array('type'=>'external', 'group' => JS_LIBRARY,  'weight' => '3')
            ),
        );

        $libraries['peanut.app'] = array(
            'title' => 'Peanut Application',
            'version' => '1.0',
            'js' => array(
                'assets/js/Tops.App/App.js' => array('group' => JS_LIBRARY, 'weight' => '4'),
            ),
            'dependencies' => array(
                array('system', 'jquery'),
                array('tops','knockoutjs'),
                array('tops','underscorejs'),
                array('tops','peanut')
                // we also need bootstrap but will rely on bootstrap module for that
            )
        );

        return $libraries;

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

    public static function GetPermissions() {
        return array(
          'execute service command' => array(
              'title' => 'Execute TOPS service',
              'description' => 'Post AJAX request to execute a service command.'
          ),
        );
    }



}