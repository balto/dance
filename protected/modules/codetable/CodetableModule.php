<?php

class CodetableModule extends EWebModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'codetable.models.*',
			'codetable.components.*',
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
	        // codetable kezelés action-jeinek hívásához milyen credential-ekre van szükség
    		'codetable'=>array(
        	    'index'=> array('codetable_list'),
        	    'getList'=> array('codetable_list'),
        	    'toggleUserIsActive'=> array('codetable_toggle_active'),
        	    'delete'=> array('codetable_delete'),
        	    'show'=> array('codetable_show'),
        	    'save'=> array('codetable_edit'),
	        ),

	    );
	}

	public static $credentials = array(
    	// törzsadat kezelés credential-jei
    	'codetable_list'=> array(
        	'title'=>        'Törzsadat lista',
        	'description'=>  'Megtekintheti a törzsadatok listáját',
    	),

    	'codetable_show'=> array(
        	'title'=>        'Törzsadat adatlap megtekintése',
        	'description'=>  'Megtekintheti egy törzsadat adatlapját',
    	),

    	'codetable_edit'=> array(
        	'title'=>        'Törzsadat módosítása',
        	'description'=>  'Szerkesztheti a törzsadatot',
    	),

    	'codetable_delete'=> array(
        	'title'=>        'Törzsadat törlése',
        	'description'=>  'Törölhet egy törzsadatot',
    	),

    	'codetable_toggle_active'=> array(
        	'title'=>        'Törzsadat aktiválása/inaktiválása',
        	'description'=>  'Aktiválhatja/inaktiválhatja a törzsadatot',
    	),

	);

	public static function getCredentials() {
	    return self::$credentials;
	}
}
