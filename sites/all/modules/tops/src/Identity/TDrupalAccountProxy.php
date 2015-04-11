<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 4/9/2015
 * Time: 3:56 PM
 */

namespace Drupal\tops\Identity;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxyInterface;

class TDrupalAccountProxy implements AccountProxyInterface {

    /**
     * @var AccountInterface
     */
    private $drupalAccount;

    public function __construct() {
        $this->drupalAccount = TDrupalAccount::GetCurrent();
    }

    /**
     * Returns the user ID or 0 for anonymous.
     *
     * @return int
     *   The user ID.
     */
    public function id()
    {
        return $this->drupalAccount->id();
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
        return $this->drupalAccount->getRoles($exclude_locked_roles);
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
        return $this->drupalAccount->hasPermission($permission);
    }

    /**
     * Returns the session ID.
     *
     * @return string|null
     *   The session ID or NULL if this user does not have an active session.
     */
    public function getSessionId()
    {
        return $this->drupalAccount->getSessionId();
    }

    /**
     * Returns the secure session ID.
     *
     * @return string|null
     *   The session ID or NULL if this user does not have an active secure session.
     */
    public function getSecureSessionId()
    {
        return $this->drupalAccount->getSecureSessionId();
    }

    /**
     * Returns the session data.
     *
     * @return array
     *   Array with the session data that belongs to this object.
     */
    public function getSessionData()
    {
        return $this->drupalAccount->getSessionData();
    }

    /**
     * Returns TRUE if the account is authenticated.
     *
     * @return bool
     *   TRUE if the account is authenticated.
     */
    public function isAuthenticated()
    {
        return $this->drupalAccount->isAuthenticated();
    }

    /**
     * Returns TRUE if the account is anonymous.
     *
     * @return bool
     *   TRUE if the account is anonymous.
     */
    public function isAnonymous()
    {
        return $this->drupalAccount->isAnonymous();
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
        return $this->drupalAccount->getPreferredLangcode($fallback_to_default);
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
        return $this->drupalAccount->getPreferredLangcode($fallback_to_default);
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
        return $this->drupalAccount->getUsername();
    }

    /**
     * Returns the email address of this account.
     *
     * @return string
     *   The email address.
     */
    public function getEmail()
    {
        return $this->drupalAccount->getEmail();
    }

    /**
     * Returns the timezone of this account.
     *
     * @return string
     *   Name of the timezone.
     */
    public function getTimeZone()
    {
        return $this->drupalAccount->getTimeZone();
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
        return $this->drupalAccount->getLastAccessedTime();
    }

    /**
     * Returns the session hostname.
     *
     * @return string
     */
    public function getHostname()
    {
        return $this->drupalAccount->getHostname();
    }

    /**
     * Sets the currently wrapped account.
     *
     * Setting the current account is highly discouraged! Instead, make sure to
     * inject the desired user object into the dependent code directly.
     *
     * A preferable method of account impersonation is to use
     * \Drupal\Core\Session\AccountSwitcherInterface::switchTo() and
     * \Drupal\Core\Session\AccountSwitcherInterface::switchBack().
     *
     * @param \Drupal\Core\Session\AccountInterface $account
     *   The current account.
     */
    public function setAccount(AccountInterface $account)
    {
        $this->drupalAccount = $account;
    }

    /**
     * Gets the currently wrapped account.
     *
     * @return \Drupal\Core\Session\AccountInterface
     *   The current account.
     */
    public function getAccount()
    {
        return $this->drupalAccount;
    }
}