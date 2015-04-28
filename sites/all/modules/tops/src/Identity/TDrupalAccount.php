<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 4/9/2015
 * Time: 7:01 AM
 *
 * Wrapper for Drupal 7 user object implementing Drupal 8 AccountInterface
 */

namespace Drupal\tops\Identity;

use Drupal;
use Drupal\Core\Session\AccountInterface;

class TDrupalAccount implements AccountInterface  {

    /**
     * @var AccountInterface[]
     */
    private static $userCache = array();

    private $drupalAccount;

    /**
     * @var AccountInterface
     */
    private static $current;

    protected function getProfileFieldValue($fieldName) {
        //todo: finish this
    }

    /**
     * @return AccountInterface
     */
    public static function GetCurrent()
    {
        if (!self::$current) {
            global $user;
            self::$current =  self::Create($user);
        }
        return self::$current;
    }

    /**
     * @return AccountInterface
     */
    public static function GetByUserName($username) {
        foreach (self::$userCache as $account) {
            if ($account->getUsername() == $username) {
                return $account;
            }
        }

        $account = user_load_by_name($username);
        return self::Create($account);
    }

    public static function Create($account)
    {
        if ($account) {
            $drupalAccount = new TDrupalAccount($account);
            self::$userCache[$drupalAccount->id()] = $drupalAccount;
            return $drupalAccount;
        }
        return null;
    }

    public static function GetById($userId = 0) {
        if (array_key_exists($userId, self::$userCache)) {
            return self::$userCache[$userId];
        }
        $account = user_load($userId);
        return self::Create($account);
    }

    public function __construct($account = null) {
        $this->drupalAccount = $account;
    }

    /**
     * Returns the user ID or 0 for anonymous.
     *
     * @return int
     *   The user ID.
     */
    public function id()
    {
        return $this->drupalAccount->uid;
    }

    public function hasIdentity() {
        return (isset($this->drupalAccount) && $this->drupalAccount->uid > 0 );
    }

    /**
     * Returns a list of roles.
     *
     * @param bool $exclude_locked_roles
     *   (optional) If TRUE, locked roles (anonymous/authenticated) are not returned.
     *
     * @return array
     *   List of role IDs.
     */
    public function getRoles($exclude_locked_roles = FALSE)
    {
        return $this->drupalAccount->roles;
    }

    /**
     * Checks whether a user has a certain permission.
     *
     * @param string $permission
     *   The permission string to check.
     *
     * @return bool
     *   TRUE if the user has the permission, FALSE otherwise.
     */
    public function hasPermission($permission)
    {
        return user_access($permission,$this->drupalAccount);
    }

    /**
     * Returns the session ID.
     *
     * @return string|null
     *   The session ID or NULL if this user does not have an active session.
     */
    public function getSessionId()
    {
        return $this->drupalAccount->sid;
    }

    /**
     * Returns the secure session ID.
     *
     * @return string|null
     *   The session ID or NULL if this user does not have an active secure session.
     */
    public function getSecureSessionId()
    {
        return $this->drupalAccount->ssid;
    }

    /**
     * Returns the session data.
     *
     * @return array
     *   Array with the session data that belongs to this object.
     */
    public function getSessionData()
    {
        return $this->drupalAccount->session;
    }

    /**
     * Returns TRUE if the account is authenticated.
     *
     * @return bool
     *   TRUE if the account is authenticated.
     */
    public function isAuthenticated()
    {
        global $user;
        return ($user->uid == $this->drupalAccount->uid && user_is_logged_in());
    }

    /**
     * Returns TRUE if the account is anonymous.
     *
     * @return bool
     *   TRUE if the account is anonymous.
     */
    public function isAnonymous()
    {
        return (!$this->hasIdentity());
        // return (!$this->isAuthenticated());
    }

    /**
     * Returns the preferred language code of the account.
     *
     * @param bool $fallback_to_default
     *   (optional) Whether the return value will fall back to the site default
     *   language if the user has no language preference.
     *
     * @return string
     *   The language code that is preferred by the account. If the preferred
     *   language is not set or is a language not configured anymore on the site,
     *   the site default is returned or an empty string is returned (if
     *   $fallback_to_default is FALSE).
     */
    public function getPreferredLangcode($fallback_to_default = TRUE)
    {
        $result = $this->hasIdentity() ? $this->drupalAccount->language : null;
        return ($result) ? $result : 'en-us';
    }

    /**
     * Returns the preferred administrative language code of the account.
     *
     * Defines which language is used on administrative pages.
     *
     * @param bool $fallback_to_default
     *   (optional) Whether the return value will fall back to the site default
     *   language if the user has no administration language preference.
     *
     * @return string
     *   The language code that is preferred by the account for administration
     *   pages. If the preferred language is not set or is a language not
     *   configured anymore on the site, the site default is returned or an empty
     *   string is returned (if $fallback_to_default is FALSE).
     */
    public function getPreferredAdminLangcode($fallback_to_default = TRUE)
    {
        return 'en-us';
    }

    /**
     * Returns the username of this account.
     *
     * By default, the passed-in object's 'name' property is used if it exists, or
     * else, the site-defined value for the 'anonymous' variable. However, a module
     * may override this by implementing
     * hook_user_format_name_alter(&$name, $account).
     *
     * @see hook_user_format_name_alter()
     *
     * @return
     *   An unsanitized string with the username to display. The code receiving
     *   this result must ensure that \Drupal\Component\Utility\String::checkPlain()
     *   is called on it before it is
     *   printed to the page.
     */
    public function getUsername()
    {
        return $this->hasIdentity() ? $this->drupalAccount->name : '';
    }

    /**
     * Returns the email address of this account.
     *
     * @return string
     *   The email address.
     */
    public function getEmail()
    {
        return $this->hasIdentity() ?  $this->drupalAccount->mail : '';
    }

    /**
     * Returns the timezone of this account.
     *
     * @return string
     *   Name of the timezone.
     */
    public function getTimeZone()
    {
        return $this->drupalAccount->timezone;
    }

    /**
     * The timestamp when the account last accessed the site.
     *
     * A value of 0 means the user has never accessed the site.
     *
     * @return int
     *   Timestamp of the last access.
     */
    public function getLastAccessedTime()
    {
        return $this->hasIdentity() ? $this->drupalAccount->timestamp : null;
    }


    private $hostName = null;

    /**
     * Returns the session hostname.
     *
     * @return string
     */
    public function getHostname()
    {
        if ($this->hostName == null) {

            $rows = db_query('SELECT hostname FROM sessions WHERE sid = :sid ',array(':sid' => session_id() ) );
            if (!empty($rows)) {
                $this->hostName = $rows[0]->hostname;
            }
        }
        return $this->hostName;
    }

    public function setDrupalAccount($account) {
        $this->drupalAccount = $account;
        $this->hostName = null;
    }

 
}