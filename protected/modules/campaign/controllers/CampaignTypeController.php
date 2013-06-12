<?php

class CampaignTypeController extends Controller
{
	public function campaignTypeDetaillistFieldDefinitions(){
		$ext = extjsHelper::ext_getHelper();
	
		$fields = array();
	
		$labels = CampaignTypeDetail::model()->attributeLabels();
		
		$fields[] = array(
			'header' => 'Id',
			'name' => 'id',
			'mapping' => '',
			'method' => '',
			'type' => '',
			'sortType' => '',
			'sortDir' => '',
			'dateFormat' => '',
			'defaultValue' => '',
			'resizable' => '',
			'align' => 'right',
			'renderer' => '',
			'groupable' => false,
			'gridColumn' => false,
			'width' => 1,
			'values' => array(
			),
		);
		
		$fields[] = array(
			'header' => $labels['moment_count'],
			'name' => 'moment_count',
			'mapping' => '',
			'method' => '',
			'type' => '',
			'sortType' => '',
			'sortDir' => '',
			'dateFormat' => '',
			'defaultValue' => '',
			'resizable' => '',
			'align' => 'center',
			'renderer' => '',
			'groupable' => false,
			'gridColumn' => true,
			'flex' => 1,
			'values' => array(
			),
				
		);
		
		$fields[] = array(
			'header' => $labels['required_moment_count'],
			'name' => 'required_moment_count',
			'mapping' => '',
			'method' => '',
			'type' => 'number',
			'sortType' => '',
			'sortDir' => '',
			'dateFormat' => '',
			'defaultValue' => '',
			'resizable' => '',
			'align' => 'center',
			'renderer' => "",
			'groupable' => false,
			'gridColumn' => true,
			'flex' => 1,
			'values' => array(
			),
		);
		
		$fields[] = array(
			'header' => $labels['required_moments'],
			'name' => 'required_moments',
			'mapping' => '',
			'method' => '',
			'type' => '',
			'sortType' => '',
			'sortDir' => '',
			'dateFormat' => '',
			'defaultValue' => '',
			'resizable' => '',
			'align' => 'center',
			'renderer' => '',
			'groupable' => false,
			'gridColumn' => true,
			'flex' => 1,
			'values' => array(
			),
				
		);
		
		return $fields;
	}
	
	
	public function listFieldDefinitions($group = false)
	{
		$ext = extjsHelper::ext_getHelper();
	
		$fields = array();
	
		$labels = CampaignType::model()->attributeLabels();
		
		$fields[] = array(
			'header' => 'Id',
			'name' => 'id',
			'mapping' => '',
			'method' => '',
			'type' => '',
			'sortType' => '',
			'sortDir' => '',
			'dateFormat' => '',
			'defaultValue' => '',
			'resizable' => '',
			'align' => 'right',
			'renderer' => '',
			'groupable' => false,
			'gridColumn' => false,
			'width' => 1,
			'values' => array(
			),
		);
		
		$fields[] = array(
				'header' => $labels['dance_type_id'],
				'name' => 'dance_type_name',
				'mapping' => '',
				'method' => '',
				'type' => 'string',
				'sortType' => '',
				'sortDir' => 'ASC',
				'dateFormat' => '',
				'defaultValue' => '',
				'resizable' => '',
				'align' => '',
				'renderer' => '',
				'groupable' => false,
				'gridColumn' => true,
				'flex' => 1,
				'values' => array(
				),
				'filter' => array(array("xtype" => "textfield",
						'filterName' => "dt.name",
				)),
		);
	
		$fields[] = array(
				'header' => $labels['name'],
				'name' => 'name',
				'mapping' => '',
				'method' => '',
				'type' => 'string',
				'sortType' => '',
				'sortDir' => 'ASC',
				'dateFormat' => '',
				'defaultValue' => '',
				'resizable' => '',
				'align' => '',
				'renderer' => '',
				'groupable' => false,
				'gridColumn' => true,
				'flex' => 1,
				'values' => array(
				),
				'filter' => array(array("xtype" => "textfield",
						'filterName' => "ct.name",
				)),
		);

		if($group){
			$fields[] = array(
				'header' => 'Group',
				'name' => 'campaign_type_group',
				'mapping' => '',
				'method' => '',
				'type' => 'string',
				'sortType' => '',
				'sortDir' => '',
				'dateFormat' => '',
				'defaultValue' => '',
				'resizable' => '',
				'align' => 'right',
				'renderer' => '',
				'groupable' => false,
				'gridColumn' => true,
				'width' => 30,
				'values' => array(
				),
			);
		}else{
			$fields[] = array(
				'header' => 'Kötelező típusok',
				'name' => 'required_campaign_types',
				'mapping' => '',
				'method' => '',
				'type' => 'string',
				'sortType' => '',
				'sortDir' => '',
				'dateFormat' => '',
				'defaultValue' => '',
				'resizable' => '',
				'align' => 'left',
				'renderer' => '',
				'groupable' => false,
				'gridColumn' => true,
				'flex' => 1,
				'values' => array(
				),
			);
			
			$fields[] = array(
				'header' => 'Alkalmak',
				'name' => 'campaign_type_moments',
				'mapping' => '',
				'method' => '',
				'type' => 'string',
				'sortType' => '',
				'sortDir' => '',
				'dateFormat' => '',
				'defaultValue' => '',
				'resizable' => '',
				'align' => 'left',
				'renderer' => '',
				'groupable' => false,
				'gridColumn' => true,
				'flex' => 1,
				'values' => array(
				),
			);
			
		}
		
		
		return $fields;
	}
	
	public function actionIndex()
	{
		$combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];

        $this->render('/campaignType/list.js', array(
            'max_per_page' => Yii::app()->params['extjs_pager_max_per_page'],
            'combo_max_per_page' => $combo_max_per_page,
        ));
	}
	
	public function actionGetCampaignTypeList()
	{
		$isCombo = $this->getParameter('isCombo', false, false);
		$isCtPermissionShow = $this->getParameter('isCtPermissionShow', 0, false);
		
		$extra_params = array();
		$this->addPagerParams($extra_params);
		$this->addOrderParams($extra_params);
		$this->addFilterParams($extra_params);
	
		$response = CampaignManager::getInstance()->getCampaignTypes($extra_params, $isCombo, $isCtPermissionShow);
	
		$this->renderText(json_encode($response));
	}
	
	public function actionGetList()
	{
		$isCombo = $this->getParameter('isCombo', false, false);
		
		$extra_params = array();
		$this->addPagerParams($extra_params);
		$this->addOrderParams($extra_params);
		$this->addFilterParams($extra_params);
	
		$response = CampaignManager::getInstance()->getCampaignTypes($extra_params,$isCombo);
	
		$this->renderText(json_encode($response));
	}

	public function actionShow()
	{
		
		$combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];
		$max_per_page = Yii::app()->params['extjs_pager_max_per_page'];
	
		$form = new CampaignTypeForm();
		$form->bindActiveRecord(CampaignType::model());

		$this->render('/campaignType/show.js', array(
			'form' => $form,
			'max_per_page' => $max_per_page,
			'combo_max_per_page' => $combo_max_per_page,
		));
	}
	
	public function actionShowCampaignType()
	{
		$combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];
		$max_per_page = Yii::app()->params['extjs_pager_max_per_page'];
	
		$this->render('/campaignType/showCampaignTypes.js', array(
			'max_per_page' => $max_per_page,
			'combo_max_per_page' => $combo_max_per_page,
		));
	}
	
	public function actionShowCampaignTypeDetail()
	{
		$combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];
		$max_per_page = Yii::app()->params['extjs_pager_max_per_page'];
	
		$form = new CampaignTypeDetailForm();
		$form->bindActiveRecord(CampaignTypeDetail::model());
	
		$this->render('/campaignType/showCampaignTypeDetail.js', array(
			'form' => $form,
			'max_per_page' => $max_per_page,
			'combo_max_per_page' => $combo_max_per_page,
		));
	}
	
	public function actionGetRecordData() {
		$id = $this->getParameter('id');
	
		$record = CampaignType::model()->findByPk($id);
	
		$form_name = 'CampaignTypeForm';
		$form = new $form_name();
		//print_r($record); exit;
		$data = array(
				$form->generateName('id') => $record->id,
				$form->generateName('name') => $record->name,	
				$form->generateName('dance_type_id') => $record->dance_type_id,	
	
				/*$form->generateName('moment_count') => (int)$record->moment_count,
				$form->generateName('required_moment_count') => (int)$record->required_moment_count,	
				$form->generateName('required_moments') => $record->required_moments,*/
		);

		$this->renderText(json_encode(array('success'=>true, 'data'=>$data)));
	}

	public function actionSave()
	{
		$requiredCampaignTypes = $this->getParameter('requiredCampaignTypes', array(), false);
		$campaignTypeDetails = $this->getParameter('campaignTypeDetails', array(), false);

		$form = new CampaignTypeForm();
		$params = $this->getParameter($form->getNameFormat());

		$response = CampaignManager::getInstance()->saveCampaignType('CampaignType', $params, $form->getNameFormat().'Form', $requiredCampaignTypes, $campaignTypeDetails);
		
		$this->renderText(json_encode($response));
	}
	
	public function actionGetRequiredCampaignTypeList()
	{
		$extra_params = array();
		$this->addPagerParams($extra_params);
		$this->addOrderParams($extra_params);
		$this->addFilterParams($extra_params);
		
		$campaignTypeId = $this->getParameter('campaignTypeId', 0, false);
	
		$response = CampaignManager::getInstance()->getRequiredCampaignTypes($campaignTypeId, $extra_params);
	
		$this->renderText(json_encode($response));
	}
	
	public function actionGetCampaignTypeDetailList()
	{
		$extra_params = array();
		$this->addPagerParams($extra_params);
		$this->addOrderParams($extra_params);
		$this->addFilterParams($extra_params);
		
		$campaignTypeId = $this->getParameter('campaignTypeId', 0, false);
	
		$response = CampaignManager::getInstance()->getCampaignTypeDetails($campaignTypeId, $extra_params);
	
		$this->renderText(json_encode($response));
	}
	
	public function actionDeleteCampaignTypeDetail(){
		$campaignTypeDetailId = $this->getParameter('campaign_type_detail_id', 0);
		
		$response = CampaignManager::getInstance()->deleteCampaignTypeDetail($campaignTypeDetailId);
	
		$this->renderText(json_encode($response));
		
	}
	
	public function actionDeleteCampaignType(){
		$id = $this->getParameter('id');
	
		$response = CampaignTypeManager::getInstance()->delete($id);
	
		$this->renderText(json_encode($response));
	}
}