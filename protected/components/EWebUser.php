<?php
/**
 * EWebUser class file, a CwebUser kiegészítése kényelmi és cache-elési funkciókkal
 *
 */

class EWebUser extends CWebUser
{
    const PERMISSIONS_NAMESPACE = 'permissions';
    //const CULTURE_NAMESPACE = 'culture';

    // sikeres autentikálás után kiolvasott adatbázis user
    private $db_user = null;
    private $permissions = array();
    //private $culture = null;

    /**
    * Initializes the application component.
    * This method overrides the parent implementation by starting session,
    * performing cookie-based authentication if enabled, and updating the flash variables.
    */
    public function init()
    {
        parent::init();

        if ($this->isAuthenticated()) {
            $this->db_user = User::model()->findByPk($this->id);
            $this->permissions = $this->getState(self::PERMISSIONS_NAMESPACE);
            //$this->setCulture($this->getState(self::CULTURE_NAMESPACE));

            // beállítjuk a láthatóságra szűrés használatát
            VisibilityManager::$useVisibility = true;
        } /*else {
            $this->setCulture(Yii::app()->request->getDefaultLanguage());
        }*/
    }

	/**
	 * This method is called before logging in a user.
	 * You may override this method to provide additional security check.
	 * For example, when the login is cookie-based, you may want to verify
	 * that the user ID together with a random token in the states can be found
	 * in the database. This will prevent hackers from faking arbitrary
	 * identity cookies even if they crack down the server private key.
	 * @param mixed $id the user ID. This is the same as returned by {@link getId()}.
	 * @param array $states a set of name-value pairs that are provided by the user identity.
	 * @param boolean $fromCookie whether the login is based on cookie
	 * @return boolean whether the user should be logged in
	 * @since 1.1.3
	 */
	protected function beforeLogin($id,$states,$fromCookie)
	{
		return true;
	}

	/**
	* Logs in a user.
	*
	* The user identity information will be saved in storage that is
	* persistent during the user session. By default, the storage is simply
	* the session storage. If the duration parameter is greater than 0,
	* a cookie will be sent to prepare for cookie-based login in future.
	*
	* Note, you have to set {@link allowAutoLogin} to true
	* if you want to allow user to be authenticated based on the cookie information.
	*
	* @param IUserIdentity $identity the user identity (which should already be authenticated)
	* @param integer $duration number of seconds that the user can remain in logged-in status. Defaults to 0, meaning login till the user closes the browser.
	* If greater than 0, cookie-based login will be used. In this case, {@link allowAutoLogin}
	* must be set true, otherwise an exception will be thrown.
	*/
	public function login($identity, $duration=0) //, $culture)
	{
	    $this->db_user = $identity->getDbUser();
	    //$this->culture = $culture;

	    parent::login($identity, $duration);
	}

	/**
	 * This method is called after the user is successfully logged in.
	 * You may override this method to do some postprocessing (e.g. log the user
	 * login IP and time; load the user permission information).
	 * @param boolean $fromCookie whether the login is based on cookie.
	 * @since 1.1.3
	 */
	protected function afterLogin($fromCookie)
	{
	    //$visibility = UserComponentManager::getInstance()->getVisibility($user);
	    //$this->setAttribute('visibility', $visibility);

	    // session-be mentjük a user összes jogát
	    $um = UserManager::getInstance();
	    $user_group_permissions = $um->getUserGroupPermissions($this->getId(), array(), false, 'name');
	    $user_group_permissions = $user_group_permissions['data'];
	    $user_own_permissions = $um->getUserOwnPermissions($this->getId(), array(), false, 'name');
	    $user_own_permissions = $user_own_permissions['data'];

	    $all_permissions = $user_group_permissions + $user_own_permissions;
	    $this->setState(self::PERMISSIONS_NAMESPACE, $all_permissions);

	    // A User táblába beírjuk a session_id-t, hogy a Felhasználók táblázatban ki tudjuk majd írni az utolsó aktivitást
	    $this->db_user->failed_logins = null; //0;
	    $this->db_user->session_id = session_id();
	    $this->db_user->last_login = sfDate::getInstance()->formatTimeZoneDbDateTime();

	    $success = $this->db_user->save();
	    if (!$success) DBManager::getInstance()->logModelError($this->db_user);

	    // TODO: a user-hez lementett nyelvet itt kell adatbázisból felolvasni és besetelni
	    //$this->setState(self::CULTURE_NAMESPACE, $this->culture);
	    //$this->setCulture($this->culture);
	}

	/**
	 * This method is invoked when calling {@link logout} to log out a user.
	 * If this method return false, the logout action will be cancelled.
	 * You may override this method to provide additional check before
	 * logging out a user.
	 * @return boolean whether to log out the user
	 * @since 1.1.3
	 */
	protected function beforeLogout()
	{
		return true;
	}

	/**
	 * This method is invoked right after a user is logged out.
	 * You may override this method to do some extra cleanup work for the user.
	 * @since 1.1.3
	 */
	protected function afterLogout()
	{
	    if (isset($this->db_user)) {
            $this->db_user->session_id = null;

            $success = $this->db_user->save();
            if (!$success) DBManager::getInstance()->logModelError($this->db_user);
        }
	}

	public function isAuthenticated() {
	    return !$this->isGuest;
	}

	/**
	 * Returns the value of a variable that is stored in user session.
	 *
	 * This function is designed to be used by CWebUser descendant classes
	 * who want to store additional user information in user session.
	 * A variable, if stored in user session using {@link setState} can be
	 * retrieved back using this function.
	 *
	 * @param string $key variable name
	 * @param mixed $defaultValue default value
	 * @return mixed the value of the variable. If it doesn't exist in the session,
	 * the provided default value will be returned
	 * @see setState
	 */
	 public function getAttribute($key,$defaultValue=null) {
	    return $this->getState($key,$defaultValue);
	}

	/**
	* Stores a variable in user session.
	*
	* This function is designed to be used by CWebUser descendant classes
	* who want to store additional user information in user session.
	* By storing a variable using this function, the variable may be retrieved
	* back later using {@link getState}. The variable will be persistent
	* across page requests during a user session.
	*
	* @param string $key variable name
	* @param mixed $value variable value
	* @param mixed $defaultValue default value. If $value===$defaultValue, the variable will be
	* removed from the session
	* @see getState
	*/
	public function setAttribute($key,$value,$defaultValue=null)
	{
	    $this->setState($key, $value, $defaultValue);
	}

	public static function checkPassword(User $db_user, $password)
	{
	    $salted_password = call_user_func_array($db_user->algorithm, array($db_user->salt . $password));

	    return $db_user->password == $salted_password;
	}

	public static function setUserPasswordFields(User $db_user, $password) {
	    if (!$password && 0 == strlen($password)) {
	        return;
	    }

	    if (!$salt = $db_user->salt) {
	        $salt = md5(rand(100000, 999999).$db_user->username);
	        $db_user->salt = $salt;
	    }

	    if (!$salt = $db_user->algorithm) {
	        $db_user->algorithm = 'sha1';
	    }

	    $db_user->password = call_user_func_array($db_user->algorithm, array($db_user->salt . $password));
	}

	public function getDbUser() {
	    return $this->db_user;
	}

	/**
	* Returns true if user has credential.
	*
	* @param  mixed $credentials
	* @param  bool  $useAnd       specify the mode, either AND or OR
	* @return bool
	*
	*/
	public function hasCredential($credentials, $useAnd = true)
	{
	    // ha a bejelebtkezett felhasználó superadmin, akkor mindenhez van joga
	    if ($this->db_user && $this->db_user->is_super_admin) {
	        return true;
	    }

	    if (!is_array($credentials))
	    {
	        return in_array($credentials, array_keys($this->permissions));
	    }

	    // now we assume that $credentials is an array
	    $test = false;

	    foreach ($credentials as $credential)
	    {
	        // recursively check the credential with a switched AND/OR mode
	        $test = $this->hasCredential($credential, $useAnd ? false : true);

	        if ($useAnd)
	        {
	            $test = $test ? false : true;
	        }

	        if ($test) // either passed one in OR mode or failed one in AND mode
	        {
	            break; // the matter is settled
	        }
	    }

	    if ($useAnd) // in AND mode we succeed if $test is false
	    {
	        $test = $test ? false : true;
	    }

	    return $test;
	}

	public function getPermissions() {
	    return $this->permissions;
	}

	/*public function setCulture($culture)
	{
	    Yii::app()->language = $culture;
	}*/
}
