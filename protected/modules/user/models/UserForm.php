<?php

/**
 */
class UserForm extends EFormModel
{
	public $id;
	public $csrf_token;
	public $username;
	public $loginname;
	public $password;
	public $password_again;
	public $password_old;
	public $is_super_admin;

	private $err_user_not_exist;
    private $err_equal;
	private $err_old_valid;
	private $err_login_valid;
	private $err_no_password_for_new_user;

    public function init()
    {
        $this->err_user_not_exist = Yii::t('msg', 'Nem létezik a felhasználó');
        $this->err_equal = Yii::t('msg', 'A két jelszó nem egyezik');
        $this->err_old_valid = Yii::t('msg', 'Jelszóváltoztatáshoz kérjük adja meg helyesen jelenlegi jelszavát!');
        $this->err_login_valid = Yii::t('msg', 'A bejelentkezési azonosító módosításához kérjük adja meg helyesen jelenlegi jelszavát!');
        $this->err_no_password_for_new_user = Yii::t('msg', 'A jelszó megadása kötelező');

        parent::init();
    }

	/**
	 * Declares the validation rules.
	 * The rules state that loginname and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			array('loginname', 'CRequiredValidator'),
			array('username', 'CStringValidator', 'min'=>5, 'on'=>'user_admin'),
			array('loginname', 'CStringValidator', 'min'=>3),
			array('password_old', 'checkPasswordsChangeOwn', 'on'=>'change_own'),
			array('is_super_admin', 'changeIfSessionUserIsSuperAdmin', 'message'=>'Csak SuperAdmin módosíthatja másik felhasználó SuperAdmin jogát!'),
		    array('password', 'CRegularExpressionValidator', 'allowEmpty'=>true, 'pattern'=>'/^'.Yii::app()->params['password_validator_pattern'].'$/', 'message'=>Yii::app()->params['password_validator_msg']),
			array('password_again', 'compareNewPasswords', 'message'=>$this->err_equal),
			array('id', 'CSafeValidator'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
            'username'=>                Yii::t('msg','Név'),
        	'loginname'=>               Yii::t('msg','Bejelentkezési azonosító'),
            'password'=>                Yii::t('msg','Jelszó'),
            'password_again'=>          Yii::t('msg','Jelszó ismét'),
            'password_old'=>            Yii::t('msg','Régi jelszó'),
            'groups_list'=>             Yii::t('msg','Jogosultsági csoportok'),
            'permissions_list'=>        Yii::t('msg','Egyéni jogosultságok'),
            'is_active'=>               Yii::t('msg','Aktív'),
            'is_super_admin'=>          Yii::t('msg','SuperAdmin'),
            'last_login'=>              Yii::t('msg','Utolsó belépés'),
            'created_at'=>              Yii::t('msg','Létrehozva'),
		);
	}

	public function checkPasswordsChangeOwn($attribute,$params)
	{
	    $user = Yii::app()->user->getDbUser();
    	$current_loginname = $user->loginname;

	    if ( empty($this->password) && empty($this->password_old) && empty($this->password_again) ) {
            // létező felhasználó loginnév módosításához kötelező régi jelszót megadni
            if ($current_loginname != $this->loginname) {
                $this->addError('loginname', $this->err_login_valid);
            }

            $this->active_record->password = $this->getOrigActiveRecord()->password;
        } else {
            // saját jelszót módosít?
            // ha a loginnév megváltozott, de a régi jelszó üres, vagy hibás
            if (($current_loginname != $this->loginname) && (empty($this->password_old) || !Yii::app()->user->checkPassword($user, $this->password_old))) {
                $this->addError('loginname', $this->err_login_valid);
            }

            // az új jelszó ki van töltve, de a régi nem, vagy hibásan
            if (!(empty($this->password) && empty($this->password_again)) && !Yii::app()->user->checkPassword($user, $this->password_old)) {
                $this->addError('password_old', $this->err_old_valid);
            }

        }

	}

	public function compareNewPasswords($attribute,$params)
	{
	    if ($this->password != $this->password_again) {
	        $this->addError($attribute, $params['message']);
	    }
	}

	public function changeIfSessionUserIsSuperAdmin($attribute,$params)
	{
	    $session_user = Yii::app()->user->getDbUser();
            $session_user_superadmin_state = $session_user->is_super_admin;

//var_dump($this->getActiveRecord()->is_super_admin); exit;
            if($this->isGetted('is_super_admin')){
                // meglévő user
                if ($this->id) {
                        $current_user = User::model()->findByPk($this->id);
                        $current_user_superadmin_state = $current_user->is_super_admin;

                        // változtatni akar a SuperAdmin állapoton?
                        // csak SuperAdmin csinálhat ilyent!
                        if ($current_user_superadmin_state != (int)$this->is_super_admin && !$session_user_superadmin_state) {
                            $this->addError($attribute, $params['message']);
                        }
                } else {
                    // új user
                    if (!$session_user_superadmin_state && $this->is_super_admin) {
                        $this->addError($attribute, $params['message']);
                    }
                }
            }
	}

	public function save() {
	    if (!empty($this->password)) {
	        Yii::app()->user->setUserPasswordFields($this->getActiveRecord(), $this->password);
	    } else {
	        $this->getActiveRecord()->password = $this->getOrigActiveRecord()->password;
	    }

	    return parent::save();
	}
}
