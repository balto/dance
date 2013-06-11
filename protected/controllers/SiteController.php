<?php

class SiteController extends Controller
{

    protected function beforeAction($action)
    {
        if(parent::beforeAction($action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			/*if (!Yii::app()->request->isAjaxRequest && $action->getId() != 'index') {
			    $this->redirect(Yii::app()->params['system_base_url'], true);
			}*/

			return true;
		}
		else
			return false;
    }

	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'

	    $this->render('dummy');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	public function actionLogin()
	{
	    $user = Yii::app()->user;
	    $request = Yii::app()->getRequest();
	    $this->layout = false;

	    $form = new LoginForm('authentication');
	    $parameters = $this->getParameter($form->getNameFormat(), null, false);

        // HA MÁR bejelentkezési adatokat küldtek
        if (!empty($parameters)) {
            $form->attributes = $parameters; // csak a scenario-ban részt vevő attributumok kapnak értéket

            // ELLENŐRIZZÜK az adatokat
            if ($form->validate()) {

                $db_user = $form->getUserIdentity()->getDbUser();
                //Yii::app()->request->cookies['hsch_language'] = new CHttpCookie('hsch_language', $parameters['language']);

                // sikeres autentikálás után megvizsgáljuk, hogy kezdeti jelszava van-e a felhasználónak és jött-e új jelszó
                if ($db_user->is_first_password == 1) {

                    // az új jelszót most jön be
                    if (isset($parameters['new_password']) && $parameters['new_password']) {
                        $change_psw_form = new LoginForm('firstPasswordSent');
                        $change_psw_form->attributes = $parameters; // csak a scenario-ban részt vevő attributumok kapnak értéket

                        if ($change_psw_form->validate()) {

                            // save new password
                            $user->setUserPasswordFields($db_user, $parameters['new_password']);
                            $db_user->is_first_password = 0;

                            $success = $db_user->save();
                            if (!$success) DBManager::getInstance()->logModelError($this->db_user);

                            // sign in
                            $response = $this->loginUser($form);
                        } else {
                            $response = json_encode(array(
                                'success'=>false,
                                'message'=>'Jelszó módosítása nem sikerült az alábbi hibák miatt:',
                                'errors'=>DBManager::getInstance()->getModelErrors($change_psw_form),
                            ));
			            }
                    } else {
                        $response = json_encode(array("success"=>true,  "need_new_password"=>1));
		            }
                } else {
                    // sign in
                    $response = $this->loginUser($form, $db_user);
	            }

            } else {
                // szándékosan nem különböztetjük meg a hibaüzeneteket, hogy a user nem talája ki, hogy van-e ilyen nevű felhasználó a rendszerben
                $response = json_encode(array("success"=>false, "error"=>array("message"=>Yii::t('msg', "Hibás felhasználónév, vagy jelszó."))));

                $db_user = User::model()->find('loginname = :loginname', array(':loginname' => $form->attributes['loginname']));
                if ($db_user) {
                    $failed_logins = $db_user->failed_logins;

                    // sikertelen bejelentkezés után növelgetjük a számlálót
                    $db_user->failed_logins = ++$failed_logins;

                    // 5 sikertelen bejelentkezés után e-mail-t küldünk
                    $config = Yii::app()->params['failed_logins']['notify'];
                    if ($failed_logins == $config['count']) {
                        $this->sendEmailByConfig($config, $db_user);
		            }

                    // 10 sikertelen bejelentkezés után letiltjuk a usert
                    if (!$db_user->is_super_admin) {
                        $config = Yii::app()->params['failed_logins']['inactivate'];
                        $config_count = $config['count'];
                        if ($failed_logins == $config_count) {
                            $db_user->is_active = false;
                            $db_user->password = null;

                            $this->sendEmailByConfig($config, $db_user);
		                }
                    }

                    $success = $db_user->save();
                    if (!$success) DBManager::getInstance()->logModelError($db_user);

                }
            }

            $this->renderText($response);
        } else {
            // login ablak JS kódját kérték
            // BEJELENTKEZETT már a felhasználó?
            if ($user->isAuthenticated()) {
                $this->renderText('var dummy;');
            } else {
                // MÉG NEM jelentkezett be a felhasználó
                // display the login form
                $this->render('/site/login.js',array(
                    'form'=>new LoginForm,
                    'default_language' => 'hu' //Yii::app()->request->getDefaultLanguage()
                ));
	        }
        }
	}

	private function loginUser($form) {
	    $form->login();

	    $return_url = Yii::app()->user->getReturnUrl();
	    $response = json_encode(array(
        	"success"=>true,
        	"redirect"=>$return_url,
            'lang' => 'hu', //Yii::app()->language,
	    ));

	    return $response;
	}

	private function sendEmailByConfig($config, User $db_user) {
	    $env  = Yii::app()->params['customer_name'];

	    $config_mail = $config['email'];
	    $subject = sprintf($config_mail['subject-template'], $db_user->username);
	    $message = sprintf($config_mail['message-template'], $db_user->username, $db_user->loginname, $env);
	    $message .= Yii::app()->params['email']['message-footer'];

	    $from = array(Yii::app()->params['email']['senderEmail'] => Yii::app()->params['email']['senderName']);

	    $recipients = array();
	    if (isset($config['admin'])) $recipients = $config['admin'];
	    if (isset($config['user']) && $config['user']) {
	        $db_user_email = $db_user->userProfile->email;
	        if ($db_user_email && !in_array($db_user_email, $recipients)) {
	            $recipients[$db_user_email] = $db_user->username;
	        }
	    }
	    //print_r($subject. ' '.$message); print_r($recipients); exit;

	    return Mail::sendPlainTextMail($from, $recipients, $subject, $message);
	}

	private function isFileUploadForm(CHttpRequest $request) {
	    return false;
//TODO: Yii-re átírni, ha kell
/*        $pathArray = $request->getPathInfoArray();

		if (isset($pathArray['CONTENT_TYPE'])) {
			$content_type = $pathArray['CONTENT_TYPE'];
			return strpos($content_type, 'multipart/form-data;') == 0;
		} else {
			return false;
		}*/
	}



	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		//$this->redirect(Yii::app()->homeUrl);

		$this->layout = false;
		$response = json_encode(array("success"=>"true", "redirect"=> Yii::app()->homeUrl ));

		$this->renderText($response);
	}

	/**
	* Kilépést valósítja meg
	*
	* (non-PHPdoc)
	* @see plugins/sfDoctrineGuardPlugin/modules/sfGuardAuth/lib/BasesfGuardAuthActions#executeSignout()
	*/
	public function executeSignout($request)
	{
	    $this->getUser()->signOut();

	    if ($request->isXmlHttpRequest()) {
	        $request->setRequestFormat('json');
	        $signin_url = sfConfig::get('app_sf_guard_plugin_success_signin_url', $this->getUser()->getReferer($request->getReferer()));
	        $signin_url = $signin_url ? $signin_url : '@homepage';
	        $response = json_encode(array("success"=>"true", "redirect"=>"$signin_url"));
	        return $this->renderText($response);
	    } else {
	        $signout_url = sfConfig::get('app_sf_guard_plugin_success_signout_url', $request->getReferer());
	        $this->redirect('' != $signout_url ? $signout_url : '@homepage');
	    }
	}


}