<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
    private $id;
    private $db_user;

	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
	    $db_user = User::model()->findByAttributes(array('loginname'=>$this->username));
        if ($db_user===null) {
			$this->errorCode=self::ERROR_USERNAME_INVALID;

        } else if(!$db_user->is_active || Yii::app()->user->checkPassword($db_user, $this->password) === false) {
			$this->errorCode=self::ERROR_PASSWORD_INVALID;

        } else {

            $this->id = $db_user->id;
            $this->db_user = $db_user;

            //$this->setState('title', $record->title);
			$this->errorCode=self::ERROR_NONE;
        }

		return !$this->errorCode;
	}

	public function getId() {
	    return $this->id;
	}

	public function getDbUser() {
	    return $this->db_user;
	}


}