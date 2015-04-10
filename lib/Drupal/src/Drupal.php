<?php

/**
 * @file
 * Contains Drupal.
 */

// use Drupal\Core\DependencyInjection\ContainerNotInitializedException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tops\sys\TObjectContainer;
// use Drupal\Core\Url;

/**
 * Emulates Drupal 8  Static Service Container wrapper.
 *
 * Generally, code in Drupal should accept its dependencies via either
 * constructor injection or setter method injection. However, there are cases,
 * particularly in legacy procedural code, where that is infeasible. This
 * class acts as a unified global accessor to arbitrary services within the
 * system in order to ease the transition from procedural code to injected OO
 * code.
 *
 * The container is built by the kernel and passed in to this class which stores
 * it statically. The container always contains the services from
 * \Drupal\Core\CoreServiceProvider, the service providers of enabled modules and any other
 * service providers defined in $GLOBALS['conf']['container_service_providers'].
 *
 * This class exists only to support legacy code that cannot be dependency
 * injected. If your code needs it, consider refactoring it to be object
 * oriented, if possible. When this is not possible, for instance in the case of
 * hook implementations, and your code is more than a few non-reusable lines, it
 * is recommended to instantiate an object implementing the actual logic.
 *
 * @code
 *   // Legacy procedural code.
 *   function hook_do_stuff() {
 *     $lock = lock()->acquire('stuff_lock');
 *     // ...
 *   }
 *
 *   // Correct procedural code.
 *   function hook_do_stuff() {
 *     $lock = \Drupal::lock()->acquire('stuff_lock');
 *     // ...
 *   }
 *
 *   // The preferred way: dependency injected code.
 *   function hook_do_stuff() {
 *     // Move the actual implementation to a class and instantiate it.
 *     $instance = new StuffDoingClass(\Drupal::lock());
 *     $instance->doStuff();
 *
 *     // Or, even better, rely on the service container to avoid hard coding a
 *     // specific interface implementation, so that the actual logic can be
 *     // swapped. This might not always make sense, but in general it is a good
 *     // practice.
 *     \Drupal::service('stuff.doing')->doStuff();
 *   }
 *
 *   interface StuffDoingInterface {
 *     public function doStuff();
 *   }
 *
 *   class StuffDoingClass implements StuffDoingInterface {
 *     protected $lockBackend;
 *
 *     public function __construct(LockBackendInterface $lockBackend) {
 *       $this->lockBackend = $lockBackend;
 *     }
 *
 *     public function doStuff() {
 *       $lock = $this->lockBackend->acquire('stuff_lock');
 *       // ...
 *     }
 *   }
 * @endcode
 *
 * @see \Drupal\Core\DrupalKernel
 */
class Drupal {

  /**
   * The current system version.
   */
  const VERSION = '7.36';

  /**
   * Core API compatibility.
   */
  const CORE_COMPATIBILITY = '7.x';

  /**
   * Core minimum schema version.
   */
  const CORE_MINIMUM_SCHEMA_VERSION = 7000;

  /**
   * The currently active container object, or NULL if not initialized yet.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface|null
   */
  protected static $container;

  /**
   * Sets a new global container.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   A new container instance to replace the current.
   */
  public static function setContainer(ContainerInterface $container) {
    static::$container = $container;
  }

  /**
   * Unsets the global container.
   */
  public static function unsetContainer() {
    static::$container = NULL;
  }

  /**
   * Returns the currently active global container.
   *
   * @throws \Drupal\Core\DependencyInjection\ContainerNotInitializedException
   *
   * @return \Symfony\Component\DependencyInjection\ContainerInterface|null
   */
  public static function getContainer() {
    if (static::$container === NULL) {

        // Use Tops DI for Drupal 7
        static::$container = TObjectContainer::GetContainer();
        if (static::$container == null) {
            throw new \Drupal\Core\DependencyInjection\ContainerNotInitializedException('\Drupal::$container is not initialized yet. \Drupal::setContainer() must be called with a real container.');
        }
    }
    return static::$container;
  }

  /**
   * Returns TRUE if the container has been initialized, FALSE otherwise.
   *
   * @return bool
   */
  public static function hasContainer() {
    return static::$container !== NULL;
  }


  /**
   * Retrieves a service from the container.
   *
   * Use this method if the desired service is not one of those with a dedicated
   * accessor method below. If it is listed below, those methods are preferred
   * as they can return useful type hints.
   *
   * @param string $id
   *   The ID of the service to retrieve.
   * @return mixed
   *   The specified service.
   */
  public static function service($id) {
    return static::getContainer()->get($id);
  }

  /**
   * Indicates if a service is defined in the container.
   *
   * @param string $id
   *   The ID of the service to check.
   *
   * @return bool
   *   TRUE if the specified service exists, FALSE otherwise.
   */
  public static function hasService($id) {
    // Check hasContainer() first in order to always return a Boolean.
    return static::hasContainer() && static::getContainer()->has($id);
  }

  /**
   * Gets the app root.
   *
   * @return string
   *
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   */
  public static function root() {
    return static::getContainer()->get('app.root');
  }

  /**
   * Indicates if there is a currently active request object.
   *
   * @return bool
   *   TRUE if there is a currently active request object, FALSE otherwise.
   * 
   *    
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   */
  public static function hasRequest() {
    // Check hasContainer() first in order to always return a Boolean.
    return static::hasContainer() && static::getContainer()->has('request_stack') && static::getContainer()->get('request_stack')->getCurrentRequest() !== NULL;
  }

  /**
   * Retrieves the currently active request object.
   *
   * Note: The use of this wrapper in particular is especially discouraged. Most
   * code should not need to access the request directly.  Doing so means it
   * will only function when handling an HTTP request, and will require special
   * modification or wrapping when run from a command line tool, from certain
   * queue processors, or from automated tests.
   *
   * If code must access the request, it is considerably better to register
   * an object with the Service Container and give it a setRequest() method
   * that is configured to run when the service is created.  That way, the
   * correct request object can always be provided by the container and the
   * service can still be unit tested.
   *
   * If this method must be used, never save the request object that is
   * returned.  Doing so may lead to inconsistencies as the request object is
   * volatile and may change at various times, such as during a subrequest.
   *
   * @return \Symfony\Component\HttpFoundation\Request
   *   The currently active request object.
   * 
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   */
  public static function request() {
    return static::getContainer()->get('request_stack')->getCurrentRequest();
  }

  /**
   * Retrives the request stack.
   *
   * @return \Symfony\Component\HttpFoundation\RequestStack
   *   The request stack
   */
  public static function requestStack() {
    return static::getContainer()->get('request_stack');
  }

  /**
   * Retrieves the currently active route match object.
   *
   *
   *   The currently active route match object.
   */
  public static function routeMatch() {
      return null; // not implemented for D7
    // return static::getContainer()->get('current_route_match');
  }

    /**
     * Gets the current active user.
     *
     * @return \Drupal\Core\Session\AccountProxyInterface
     */
    public static function currentUser() {
        return static::getContainer()->get('current_user');
    }


    /**
   * Retrieves the entity manager service.
   *
   * @return \Drupal\Core\Entity\EntityManagerInterface
   *   The entity manager service.
   * 
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   */
  public static function entityManager() {
    return static::getContainer()->get('entity.manager');
  }

  /**
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   *
   */
  public static function database() {
    return static::getContainer()->get('database');
  }

    /**
     * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
     *
     */
  public static function cache($bin = 'default') {
    return static::getContainer()->get('cache.' . $bin);
  }

    /**
     * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
     *
     */
  public static function keyValueExpirable($collection) {
    return static::getContainer()->get('keyvalue.expirable')->get($collection);
  }

    /**
     * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
     *
     */
  public static function lock() {
    return static::getContainer()->get('lock');
  }

    /**
     * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
     *
     */
  public static function config($name) {
    return static::getContainer()->get('config.factory')->get($name);
  }

    /**
     * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
     *
     */
  public static function configFactory() {
    return static::getContainer()->get('config.factory');
  }

    /**
     * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
     *
     */
  public static function queue($name, $reliable = FALSE) {
    return static::getContainer()->get('queue')->get($name, $reliable);
  }

    /**
     * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
     *
     */
  public static function keyValue($collection) {
    return static::getContainer()->get('keyvalue')->get($collection);
  }

  /**
   * Returns the state storage service.
   *
   * Use this to store machine-generated data, local to a specific environment
   * that does not need deploying and does not need human editing; for example,
   * the last time cron was run. Data which needs to be edited by humans and
   * needs to be the same across development, production, etc. environments
   * (for example, the system maintenance message) should use \Drupal::config() instead.
   *
   * @return \Drupal\Core\State\StateInterface
   * 
   * 
   * 
   *    
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   */
  public static function state() {
    return static::getContainer()->get('state');
  }

    /**
     * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
     *
     */
  public static function httpClient() {
    return static::getContainer()->get('http_client');
  }

  /**
   * Returns the entity query object for this entity type.
   *
   * @param string $entity_type
   *   The entity type, e.g. node, for which the query object should be
   *   returned.
   * @param string $conjunction
   *   AND if all conditions in the query need to apply, OR if any of them is
   *   enough. Optional, defaults to AND.
   *
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   *
   */
  public static function entityQuery($entity_type, $conjunction = 'AND') {
    return static::getContainer()->get('entity.query')->get($entity_type, $conjunction);
  }

  /**
   * Returns the entity query aggregate object for this entity type.
   *
   * @param string $entity_type
   *   The entity type, e.g. node, for which the query object should be
   *   returned.
   * @param string $conjunction
   *   AND if all conditions in the query need to apply, OR if any of them is
   *   enough. Optional, defaults to AND.
   *
   *
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   *
   */
  public static function entityQueryAggregate($entity_type, $conjunction = 'AND') {
    return static::getContainer()->get('entity.query')->getAggregate($entity_type, $conjunction);
  }

  /**
   * Returns the flood instance.
   *
   *
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   *
   */
  public static function flood() {
    return static::getContainer()->get('flood');
  }

  /**
   * Returns the module handler.
   *
   *
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   *
   */
  public static function moduleHandler() {
    return static::getContainer()->get('module_handler');
  }

  /**
   * Returns the typed data manager service.
   *
   * Use the typed data manager service for creating typed data objects.
   *
   *
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   *
   *
   * @see \Drupal\Core\TypedData\TypedDataManager::create()
   */
  public static function typedDataManager() {
    return static::getContainer()->get('typed_data_manager');
  }

  /**
   * Returns the token service.
   *
   *
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   *
   *   The token service.
   */
  public static function token() {
    return static::getContainer()->get('token');
  }

  /**
   * Returns the url generator service.
   *
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   *   The url generator service.
   */
  public static function urlGenerator() {
    return static::getContainer()->get('url_generator');
  }

  /**
   * Generates a URL string for a specific route based on the given parameters.
   *
   * This method is a convenience wrapper for generating URL strings for URLs
   * that have Drupal routes (that is, most pages generated by Drupal) using
   * the \Drupal\Core\Url object. See \Drupal\Core\Url::fromRoute() for
   * detailed documentation. For non-routed local URIs relative to
   * the base path (like robots.txt) use Url::fromUri()->toString() with the
   * base: scheme.
   *
   * @param string $route_name
   *   The name of the route.
   * @param array $route_parameters
   *   (optional) An associative array of parameter names and values.
   * @param array $options
   *   (optional) An associative array of additional options.
   *
   * @return string
   *   The generated URL for the given route.
   *
   * @see \Drupal\Core\Routing\UrlGeneratorInterface::generateFromRoute()
   * @see \Drupal\Core\Url
   * @see \Drupal\Core\Url::fromRoute()
   * @see \Drupal\Core\Url::fromUri()
   * 
   *  NOT IMPLEMENTED FOR Drupal 7. See D8 version.

   */
  public static function url($route_name, $route_parameters = array(), $options = array()) {
    return static::getContainer()->get('url_generator')->generateFromRoute($route_name, $route_parameters, $options);
  }

  /**
   * Returns the link generator service.
   *
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   */
  public static function linkGenerator() {
    return static::getContainer()->get('link_generator');
  }

  /**
   * Renders a link with a given link text and Url object.
   *
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   */
  public static function l($text, Url $url) {
    return static::getContainer()->get('link_generator')->generate($text, $url);
  }

  /**
   * Returns the string translation service.
   *
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   */
  public static function translation() {
    return static::getContainer()->get('string_translation');
  }

  /**
   * Returns the language manager service.
   *
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   */
  public static function languageManager() {
    return static::getContainer()->get('language_manager');
  }

  /**
   * Returns the CSRF token manager service.
   *
   * The generated token is based on the session ID of the current user. Normally,
   * anonymous users do not have a session, so the generated token will be
   * different on every page request. To generate a token for users without a
   * session, manually start a session prior to calling this function.
   *
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   */
  public static function csrfToken() {
    return static::getContainer()->get('csrf_token');
  }

  /**
   * Returns the transliteration service.
   *
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   */
  public static function transliteration() {
    return static::getContainer()->get('transliteration');
  }

  /**
   * Returns the form builder service.
   *
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   */
  public static function formBuilder() {
    return static::getContainer()->get('form_builder');
  }

  /**
   * Gets the theme service.
   *
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   */
  public static function theme() {
    return static::getContainer()->get('theme.manager');
  }

  /**
   * Gets the syncing state.
   *
   * @return bool
   *   Returns TRUE is syncing flag set.
   * 
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   */
  public static function isConfigSyncing() {
    return static::getContainer()->get('config.installer')->isSyncing();
  }

  /**
   * Returns a channel logger object.
   *
   * @param string $channel
   *   The name of the channel. Can be any string, but the general practice is
   *   to use the name of the subsystem calling this.
   *
   * @return \Psr\Log\LoggerInterface
   *   The logger for this channel.
   *
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   */
  public static function logger($channel) {
    return static::getContainer()->get('logger.factory')->get($channel);
  }

  /**
   * Returns the menu tree.
   *
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   */
  public static function menuTree() {
    return static::getContainer()->get('menu.link_tree');
  }

  /**
   * Returns the path validator.
   *
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   */
  public static function pathValidator() {
    return static::getContainer()->get('path.validator');
  }

  /**
   * Returns the access manager service.
   *
   * NOT IMPLEMENTED FOR Drupal 7. See D8 version.
   *   The access manager service.
   */
  public static function accessManager() {
    return static::getContainer()->get('access_manager');
  }

}
