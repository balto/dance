<?php

class CampaignTypeDetailController extends Controller
{

	
	public function actionGetComboList()
	{
	
		$response = CampaignManager::getInstance()->getCampaignTypeDetails();
	
		$this->renderText(json_encode($response));
	}
	
	public function actionGetList()
	{
		$isCombo = $this->getParameter('isCombo', false, false);
		
		$extra_params = array();
		$this->addPagerParams($extra_params);
		$this->addOrderParams($extra_params);
		$this->addFilterParams($extra_params);
	
		$response = CampaignManager::getInstance()->getCampaignTypeDetailList($extra_params,$isCombo);
	
		$this->renderText(json_encode($response));
	}
	
	/*
	public function actionGetPermissionedCampaignTypeList()
	{
		$extra_params = array();
		$this->addPagerParams($extra_params);
		$this->addOrderParams($extra_params);
		$this->addFilterParams($extra_params);
		
		$campaignTypeId = $this->getParameter('campaignTypeId', 0, false);
	
		$response = CampaignManager::getInstance()->getPermissionedCampaignTypes($campaignTypeId, $extra_params);
	
		$this->renderText(json_encode($response));
	}
	*/
	
	
	
	/*
	public function actionShowCampaignTypePermission()
	{
		$combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];
		$max_per_page = Yii::app()->params['extjs_pager_max_per_page'];
		
		$this->render('/campaignType/showCampaignTypesPermission.js', array(
				'max_per_page' => $max_per_page,
				'combo_max_per_page' => $combo_max_per_page,
		));
	}
	*/
	
	
	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}