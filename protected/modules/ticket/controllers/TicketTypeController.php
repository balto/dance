<?php

class TicketTypeController extends Controller
{
	
	public function listFieldDefinitions()
	{
		$ext = extjsHelper::ext_getHelper();
	
		$fields = array();
	
		$labels = TicketType::model()->attributeLabels();
		
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
			'type' => 'string',
			'sortType' => '',
			'sortDir' => '',
			'dateFormat' => '',
			'defaultValue' => '',
			'resizable' => '',
			'align' => '',
			'renderer' => '',
			'groupable' => false,
			'gridColumn' => true,
			'width' => 120,
			'values' => array(
			),
			'filter' => array(array("xtype" => "textfield",
					'filterName' => "tt.moment_count",
			)),
		);
	
		$fields[] = array(
			'header' => $labels['valid_days'],
			'name' => 'valid_days',
			'mapping' => '',
			'method' => '',
			'type' => 'string',
			'sortType' => '',
			'sortDir' => '',
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
				'filterName' => "tt.valid_days",
			)),
		);
		
		$fields[] = array(
			'header' => 'Használható',
			'name' => 'joined_campaign_types',
			'mapping' => '',
			'method' => '',
			'type' => 'string',
			'sortType' => '',
			'sortDir' => '',
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
		);
		
		$fields[] = array(
			'header' => 'Jogosult',
			'name' => 'permissioned_campaign_types',
			'mapping' => '',
			'method' => '',
			'type' => 'string',
			'sortType' => '',
			'sortDir' => '',
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
		);

		return $fields;
	}

public function campaignTypelistFieldDefinitions($show_is_free = false, $is_free_check = false)
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
		
		if($show_is_free){
			$temp = array(
				'header' => 'Ingyenes',
				'name' => 'is_free',
				'mapping' => '',
				'method' => '',
				//'type' => 'string',
				'type' => 'string',
				'sortType' => '',
				'sortDir' => '',
				'dateFormat' => '',
				'defaultValue' => '',
				'resizable' => '',
				'align' => 'right',
				'renderer' => '',
				//'renderer' => 'function(value,c,record){return value=="1" ? "igen":"nem" ;}',
				'groupable' => false,
				'gridColumn' => true,
				'width' => 50,
				'values' => array(
				),
			);

			if($is_free_check){
				$temp['editor'] = array(
					'xtype' => 'checkboxfield'
				);
			}
			else{
				$temp['renderer'] = 'function(value,c,record){return value=="1" ? "igen":"nem" ;}';
			}

			$fields[] = $temp;
		}
		
		return $fields;
	}
	
	public function actionIndex()
	{
		$combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];

        $this->render('/ticketType/list.js', array(
            'max_per_page' => Yii::app()->params['extjs_pager_max_per_page'],
            'combo_max_per_page' => $combo_max_per_page,
        ));
	}
	
	/*public function actionGetList()
	{
		$extra_params = array();
		$this->addPagerParams($extra_params);
		$this->addOrderParams($extra_params);
		$this->addFilterParams($extra_params);
	
		$response = MemberManager::getInstance()->getMembers($extra_params);
	
		$this->renderText(json_encode($response));
	}*/
	
	public function actionGetList()
	{
		$isCombo = $this->getParameter('isCombo', false, false);
		
		$extra_params = array();
		$this->addPagerParams($extra_params);
		$this->addOrderParams($extra_params);
		$this->addFilterParams($extra_params);
	
		$response = TicketManager::getInstance()->getTicketTypes($extra_params, $isCombo);
	
		$this->renderText(json_encode($response));
	}
	
	public function actionGetJoinCampaignTypeList()
	{
		$extra_params = array();
		$this->addPagerParams($extra_params);
		$this->addOrderParams($extra_params);
		$this->addFilterParams($extra_params);
		
		$ticketTypeId = $this->getParameter('TicketTypeId', 0, false);
		$isMain = $this->getParameter('isMain', 0, false);
	
		$response = TicketManager::getInstance()->getJoinCampaignTypes($ticketTypeId, $isMain, $extra_params);
	
		$this->renderText(json_encode($response));
	}

	public function actionShow()
	{
		$combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];
		$max_per_page = Yii::app()->params['extjs_pager_max_per_page'];
	
		$form = new TicketTypeForm();
		$form->bindActiveRecord(TicketType::model());
	
		$this->render('/ticketType/show.js', array(
				'form' => $form,
				'max_per_page' => $max_per_page,
				'combo_max_per_page' => $combo_max_per_page,
		));
	}

	public function actionShowCampaignType()
	{
		$combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];
		$max_per_page = Yii::app()->params['extjs_pager_max_per_page'];
	
		$this->render('/ticketType/showCampaignTypes.js', array(
				'max_per_page' => $max_per_page,
				'combo_max_per_page' => $combo_max_per_page,
		));
	}
	
	public function actionShowCampaignTypePermission()
	{
		$combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];
		$max_per_page = Yii::app()->params['extjs_pager_max_per_page'];
		
		$this->render('/ticketType/showCampaignTypesPermission.js', array(
				'max_per_page' => $max_per_page,
				'combo_max_per_page' => $combo_max_per_page,
		));
	}
	
	public function actionSave()
	{
		$JoinCampaignTypes = $this->getParameter('JoinCampaignTypes', array(), false);
		$permissionedCampaignTypes = $this->getParameter('permissionedCampaignTypes', array(), false);
		
		$form = new TicketTypeForm();
		$params = $this->getParameter($form->getNameFormat());

		$response = TicketManager::getInstance()->saveTicketType('TicketType', $params, $form->getNameFormat().'Form', $JoinCampaignTypes, $permissionedCampaignTypes);

		
		$this->renderText(json_encode($response));
	}
	
	public function actionGetRecordData() {
		$id = $this->getParameter('id');
	
		$record = TicketType::model()->findByPk($id);
	
		$form_name = 'TicketTypeForm';
		$form = new $form_name();
		//print_r($record); exit;
		$data = array(
				$form->generateName('id') => $record->id,
				$form->generateName('moment_count') => $record->moment_count,	
				$form->generateName('is_daily') => $record->is_daily,
				$form->generateName('valid_days') => $record->valid_days,
		);

		$this->renderText(json_encode(array('success'=>true, 'data'=>$data)));
	}
	
	public function actionDeleteTicketType(){
		$id = $this->getParameter('id');
	
		$response = TicketManager::getInstance()->delete($id);
	
		$this->renderText(json_encode($response));
	}
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