<?php

class CampaignModule extends CWebModule
{
	public $secure = true;
	
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'campaign.models.*',
			'campaign.components.*',
		));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			
			$controller->layout = false;
			return true;
		}
		else
			return false;
	}
	
	public static function getModuleCredentials() {
		return array(
			'campaignType'=>array(
				'index'=> array('admin_campaign_type_show'),
				'getList'=> array('admin_campaign_type_show'),
				'getCampaignTypeList'=> array('admin_campaign_type_show'), // mindenki, aki láthatja egyúttal módosíthatja is a profilját
				'getRequiredCampaignTypeList'=> array('admin_campaign_type_show'),
				'show'=> array('admin_campaign_type_show'),
				'showCampaignType'=> array('admin_campaign_type_show'),
				'getRecordData'=> array('admin_campaign_type_show'),
				'save'=> array('admin_campaign_type_edit'),
				'show'=> array('admin_campaign_type_show'),
				
			),
		);
	}
	
	public static $credentials = array(
		'admin_campaign_type_show'=> array(
			'title'=>        'Admin kampány típusok megtekintése',
			'description'=>  'ADMIN KAMPÁNY TÍPUSOK - Megtekintheti az admin felületen a kampány típusokat',
		),

		'admin_campaign_type_edit'=> array(
			'title'=>        'Admin kampány típusok szerkesztése',
			'description'=>  'ADMIN KAMPÁNY TÍPUSOK - Szerkesztheti az admin felületen a kampány típusokat',
		),
	);
	
	public static function getCredentials() {
		return self::$credentials;
	}
}
