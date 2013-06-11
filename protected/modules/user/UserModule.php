<?php

class UserModule extends EWebModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'user.models.*',
			'user.components.*',
		));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
		    if (!Yii::app()->request->isAjaxRequest) $controller->redirect(array('/index'));

		    $controller->layout = false;

			return true;
		}
		else
			return false;
	}

	/**
	 *
	 *  array(array('user_list'), array('user_show')) jelentése user_list VAGY user_show jog kell hozzá
	 *  array('user_list', 'user_show') jelentése user_list ÉS user_show jog kell hozzá
	 */
	public static function getModuleCredentials() {
	    return array(
	        // user modul default controller action-jei
	        'defaultUser'=>array(
        	    'profile'=> array('user_profile_show'),
        	    'fillProfileForm'=> array('user_profile_show'),
        	    'saveProfile'=> array('user_profile_show'), // mindenki, aki láthatja egyúttal módosíthatja is a profilját
	    		'getAssociatedGroups'=> array('user_profile_show'),
	    		'getAllPermissions'=> array('user_profile_show'),
	        ),
	        // user kezelés action-jeinek hívásához milyen credential-ekre van szükség
    		'user'=>array(
        	    'index'=> array('user_list'),
        	    'getList'=> array('user_list'),
        	    'toggleUserIsActive'=> array('user_toggle_active'),
        	    'toggleUserIsSuperAdmin'=> array('user_edit'),
        	    'generatePassword'=> array('user_generate_password'),
        	    'delete'=> array('user_delete'),
        	    'show'=> array(array('user_show'),array('editIndividualPermissions')),
        	    'save'=> array('user_edit'),
	        ),

	        // user_group kezelés action-jeinek hívásához milyen credential-ekre van szükség
	        'userGroup'=> array(
	    		'index'=> array('user_group_list'),
        	    'show'=> array('user_group_show'),
        	    'getAvailablePermissions'=> array('user_group_show'),
        	    'getAssociatedPermissions'=> array('user_group_show'),
	    		'fillForm'=> array('user_group_show'),
        	    'getList'=> array('user_group_list'),
	    		'delete'=> array('user_group_delete'),
	    		'save'=> array('user_group_edit'),
    	    ),

	        // permission kezelés action-jeinek hívásához milyen credential-ekre van szükség
	        'permission'=>array(
	    		'index'=> array('user_permission_list'),
	            'getList'=> array('user_permission_list'),
        	    'show'=> array('user_permission_show'),
        	    'fillForm'=> array('user_permission_show'),
        	    'reload'=> array('user_permission_reload'),
	        ),


	    );
	}

	public static $credentials = array(
    	// user kezelés credential-jei
    	'user_list'=> array(
        	'title'=>        'Felhasználó lista',
        	'description'=>  'Megtekintheti a felhasználók listáját',
    	),

    	'user_show'=> array(
        	'title'=>        'Felhasználó adatlap megtekintése',
        	'description'=>  'Megtekintheti egy felhasználó adatlapját',
    	),

    	'user_profile_show'=> array(
        	'title'=>        'Felhasználó saját adatlapjának megtekintése',
        	'description'=>  'Megtekintheti a felhasználó a saját adatlapját',
    	),

    	'user_edit'=> array(
        	'title'=>        'Felhasználó adatok módosítása',
        	'description'=>  'Szerkesztheti egy felhasználó adatait',
    	),

    	'user_delete'=> array(
        	'title'=>        'Felhasználó törlése',
        	'description'=>  'Törölhet egy felhasználót',
    	),

    	'user_generate_password'=> array(
        	'title'=>        'Felhasználó számára új kezdeti jelszó generálása',
        	'description'=>  'Generálhat új jelszót a felhasználónak',
    	),

    	'user_toggle_active'=> array(
        	'title'=>        'Felhasználó aktiválása/letiltása',
        	'description'=>  'Aktiválhatja/letilthatja a felhasználót',
    	),

    	// user_group kezelés credential-jei
    	'user_group_list'=> array(
        	'title'=>        'Felhasználó csoport lista',
        	'description'=>  'Megtekintheti a felhasználó csoportok listáját',
    	),

    	'user_group_show'=> array(
        	'title'=>        'Felhasználó csoport adatlap megtekintése',
        	'description'=>  'Megtekintheti egy felhasználó csoport adatlapját',
    	),

    	'user_group_edit'=> array(
        	'title'=>        'Felhasználó csoport adatainak szerkesztése',
        	'description'=>  'Módosíthatja egy felhasználó csoport adatait',
    	),

    	'user_group_delete'=> array(
        	'title'=>        'Felhasználó csoport törlése',
        	'description'=>  'Törölhet egy felhasználó csoportot',
    	),

    	// permission kezelés credential-jei
    	'user_permission_list'=> array(
        	'title'=> 'Jogosultság lista',
        	'description'=> 'Megtekintheti a jogosultságok listáját',
    	),

    	'user_permission_show'=> array(
        	'title'=> 'Jogosultság adatlap megtekintése',
        	'description'=> 'Megtekintheti egy jogosultság adatlapját',
    	),

    	'user_permission_reload'=> array(
        	'title'=> 'Jogosultságok újratöltése',
        	'description'=> 'A meglévő jogosultságok frissítése, új jogosultságok importálása',
    	),
        
        'editIndividualPermissions' => array(
            'title' => 'Felhasználó egyéni jogok szerkesztése',
            'description' => 'Jogosultság a felhasználókhoz tartozó egyéni jogok szerkesztéséhez',
        ),

	);

	public static function getCredentials() {
	    return self::$credentials;
	}
}
