<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 3/12/2015
 * Time: 4:35 AM
 */

namespace Drupal\tops\Identity;



use Drupal\Core\Session\AccountInterface;
use Tops\cache\ITopsCache;
use Tops\cache\TSessionCache;
use Tops\sys;
use Drupal;
use Tops\sys\TAbstractUser;

/**
 * Class TDrupalUser
 * @package Drupal\tops\Identity
 */
class TDrupalUser extends TAbstractUser  {


    protected function test() {
        return 'drupal';
    }


    function __construct(AccountInterface $user = null) {
        if (isset($user)) {
            $this->loadDrupalUser($user);
        }
        $result = $this->test();
    }

    /**
     * @var AccountInterface
     */
    private   $drupalUser;



    protected function loadDrupalUser(AccountInterface $account = null)
    {
        $test = $this->test();
        $this->drupalUser = $account;
        if (empty($account)) {
            return;
        }

        // todo: this may not work in Drupal 8
        if ($_SERVER['SCRIPT_NAME'] === '/cron.php') {
            $this->authenticated = true;
            $this->userName = 'cron';
            $this->isCurrentUser = true;
            return;
        }

        $currentUser = TDrupalAccount::GetCurrent();

        if ($account == null) {
            $this->drupalUser = $account;
            $this->isCurrentUser = true;
        } else {
            $this->isCurrentUser = $account->id() == $currentUser->id();
        }


        if ($account) {
            $this->email = $account->getEmail();
            $this->userName = $account->getUsername();
            $this->id = $account->id();
        }

    }

    /**
     * @var ITopsCache
     */
    private static $profileCache;

    /**
     * @return ITopsCache
     */
    public static function getProfileCache()
    {
        if (!isset(self::$profileCache)) {
            self::$profileCache = new TSessionCache();
        }
        // self::$profileCache->FlushCachedItems();
        return self::$profileCache;
    }

    private function getCachedProfile() {
        $cache = self::getProfileCache();
        $result = $cache->Get('users.'.$this->userName);
        return $result;
    }

    private function cacheProfile() {
        $cache = self::getProfileCache();
        $result = $cache->Set('users.'.$this->userName,$this->profile,20);
        return $result;
    }



    /**
     * @param AccountInterface $user
     *
     * Assign firstName, lastName, middleName and pictureFile
     *
     * This version for Drupal 7 - might work with 8, we'll see...
     */
    protected function loadProfile() {
        $this->profile = $this->getCachedProfile();
        if (empty($this->profile)) {
            $this->profile = array();
            $drupalUser = user_load($this->getId());
            if ($drupalUser != null) {
                $vars = get_object_vars($drupalUser);
                $keys = array_keys($vars);
                foreach ($keys as $key) {
                    if (substr($key, 0, 6) === "field_") {
                        $item = $vars[$key];
                        $hasValue = isset($item['und'][0]['value']);
                        if ($hasValue) {
                            $value = $item['und'][0]['value'];
                            $fieldName = substr($key, 6);
                            $this->profile[$fieldName] = $value;
                        }

                    }
                }
            }
            $this->cacheProfile();
        }
    }


    /**
     * @param $id
     * @return mixed
     */
    public function loadById($id)
    {
        // for Drupal 8
        // $drupalAccount = \Drupal\user\Entity\User::load($id);
        // for Drupal 7
        $drupalAccount = TDrupalAccount::GetById($id);

        $this->loadDrupalUser($drupalAccount);
    }

    /**
     * @param $userName
     * @return mixed
     */
    public function loadByUserName($userName)
    {
        $account = TDrupalAccount::GetByUserName($userName);
        $this->loadDrupalUser($account);
    }

    /**
     * @return mixed
     */
    public function loadCurrentUser()
    {
        $account = TDrupalAccount::GetCurrent();
        $this->loadDrupalUser($account);
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        if ($this->drupalUser) {
            return ($this->drupalUser->id() == 1 || $this->isMemberOf('Administator'));
        }
        return false;
    }

    /**
     * @return string[]
     */
    public function getRoles()
    {
        if ($this->drupalUser) {
            return $this->drupalUser->getRoles();
        }
        return array();
    }

    /**
     * @param $roleName
     * @return bool
     */
    public function isMemberOf($roleName)
    {
        if ($this->drupalUser) {
            $roles = $this->drupalUser->getRoles();
            return in_array($roleName,$roles);
        }
        return false;
    }

    /**
     * @param string $value
     * @return bool
     */
    public function isAuthorized($value = '')
    {
        if ($this->drupalUser) {
            return $this->drupalUser->hasPermission($value);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
        if ($this->drupalUser) {
            return $this->drupalUser->isAuthenticated();
        }
        return false;
    }


}