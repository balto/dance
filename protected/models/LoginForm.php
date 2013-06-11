<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends EFormModel
{
    public $loginname;
	public $password;
	//public $language;
	public $new_password;
	public $new_password_again;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that loginname and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// loginname and password are required
			array('loginname, password', 'CRequiredValidator', 'on' => 'authentication'),
			// password needs to be authenticated
			array('password', 'authenticate', 'on' => 'authentication'),
			// language must be in the application languages list
		    //array('language', 'validLanguage', 'message' => Yii::t('msg','Az alkalmazás a választott nyelven nem érhető el!')),
			array('new_password', 'CRegularExpressionValidator', 'allowEmpty'=>false, 'pattern'=>'/^'.Yii::app()->params['password_validator_pattern'].'$/', 'message'=>Yii::app()->params['password_validator_msg'], 'on' => 'firstPasswordSent'),
			array('new_password_again', 'compareNewPasswords', 'message'=>Yii::t('msg','A megismételt jelszó nem egyezik!'), 'on' => 'firstPasswordSent'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
		    'loginname' =>          Yii::t('msg','Felhasználónév'),
			'password' =>           Yii::t('msg','Jelszó'),
			'new_password' =>       Yii::t('msg','Új jelszó'),
			'new_password_again' => Yii::t('msg','Új jelszó ismét'),
			//'language' =>           Yii::t('msg','Nyelv'),
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
		    $this->_identity = new UserIdentity($this->loginname, $this->password);
			if (!$this->_identity->authenticate()) {
				$this->addError('password',Yii::t('msg','Rossz bejelentkezési név vagy jelszó.'));
    		}
    	}
	}

	/*public function validLanguage($attribute,$params)
	{
	    $app_languages = Yii::app()->params['languages'];
	    if (!array_key_exists($this->language, $app_languages)) {
	        $this->addError($attribute,$params['message']);
	    }
	}*/

	/**
	 * Logs in the user using the given loginname and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity===null)
		{
			$this->_identity = new UserIdentity($this->loginname,$this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
		{
			$duration=0; //$this->rememberMe ? 3600*24*30 : 0; // 30 days
			//Yii::app()->user->login($this->_identity, $duration, $this->language);
            Yii::app()->user->login($this->_identity, $duration);

			return true;
		}
		else
			return false;
	}

	public function compareNewPasswords($attribute,$params)
	{
	    if ($this->new_password != $this->new_password_again) {
	        $this->addError($attribute,$params['message']);
}
	}

	public function getUserId() {
	    return $this->_identity->getId();
	}

	public function getUserIdentity() {
	    return $this->_identity;
	}
}
