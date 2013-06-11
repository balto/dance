<?php

class CampaignController extends Controller
{
	public function listCampaignFieldDefinitions() {
		$ext = extjsHelper::ext_getHelper();
	
		$fields = array();
	
		$labels = Campaign::model()->attributeLabels();
		$labels_ct = CampaignType::model()->attributeLabels();
		$labels_ctd = CampaignTypeDetail::model()->attributeLabels();
		
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
			'header' => 'Campaign Type Id',
			'name' => 'campaign_type_id',
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
			'header' => 'Campaign Moment Count',
			'name' => 'campaign_moment_count',
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
			'header' => $labels_ct['dance_type_id'],
			'name' => 'dance_type_name',
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
					'filterName' => "dt.name",
			)),
		);
		
		$fields[] = array(
			'header' => $labels_ctd['campaign_type_id'],
			'name' => 'campaign_type_name',
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
				'filterName' => "ct.name",
			)),
		);
	
		$fields[] = array(
			'header' => $labels['location_id'],
			'name' => 'location_name',
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
				'filterName' => "l.name",
			)),
		);
		
		$fields[] = array(
			'header' => $labels['start_datetime'],
			'name' => 'start_date',
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
			'width' => 100,
			'values' => array(
			),
			'filter' => array(array("xtype" => "textfield",
				'filterName' => "c.start_date",
			)),
		);
		
		$fields[] = array(
				'header' => $labels['end_datetime'],
				'name' => 'end_datetime',
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
				'width' => 100,
				'values' => array(
				),
				'filter' => array(array("xtype" => "textfield",
						'filterName' => "c.end_datetime",
				)),
		);
		
		$fields[] = array(
			'header' => 'Alk.',
			'name' => 'campaign_type_detail_moment_count',
			'mapping' => '',
			'method' => '',
			'type' => 'string',
			'sortType' => '',
			'sortDir' => '',
			'dateFormat' => '',
			'defaultValue' => '',
			'resizable' => '',
			'align' => '',
			'renderer' => 'function(value,c,record){return value+"/"+record.data.campaign_moment_count;}',
			'groupable' => false,
			'gridColumn' => true,
			'width' => 35,
			'values' => array(
			),
		);
		
		$fields[] = array(
			'header' => 'Befejezve',
			'name' => 'completed',
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
			'gridColumn' => false,
			'width' => 1,
			'values' => array(
			),
		);
		
		return $fields;
	}

	public function getRulesTreeFieldDefinitions() {			
        $fields = array();

        $fields[] = array(
            'name' => 'id',
            'type' => '',
            'header' => 'Azonosító',
            'xtype' => '',
            'sortDir' => '',
            'gridColumn' => false,
        );

        $fields[] = array(
            'name' => 'text',
            'header' => 'Megnevezés',
            'xtype' => 'treecolumn',
            //'sortDir' => 'asc',
           // 'gridColumn' => true,
            'flex' => 1,
        );
		
		$fields[] = array(
            'name' => 'price',
            'header' => 'Ár',
        );
		
		$fields[] = array(
            'name' => 'percent',
            'header' => 'Százalék',
        );
		
		$fields[] = array(
            'name' => 'full_price',
            'header' => 'Összeg',
        );
		
		$fields[] = array(
            'name' => 'link_id',
            'header' => 'Azon.',
        );
		
		$fields[] = array(
            'name' => 'expense_price',
            'header' => 'Kiadás',
        );
		
		$fields[] = array(
            'name' => 'price_type',
            'header' => 'Összeg típus',
        );

        return $fields;
    }

	public function actionTest()
	{
		$campaignId = 2;
		
		$combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];
		$max_per_page = Yii::app()->params['extjs_pager_max_per_page'];
	
		//CampaignManager::getInstance()->startPriceRulesForCampaign($campaignId);
	
		$this->render('/campaign/showCampaignPriceRulesTest.js', array(
				'max_per_page' => $max_per_page,
				'combo_max_per_page' => $combo_max_per_page,
				'campaignId' => $campaignId,
		));
	}

	
	public function actionIndex()
	{
		$combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];

        $this->render('/campaign/list.js', array(
            'max_per_page' => Yii::app()->params['extjs_pager_max_per_page'],
            'combo_max_per_page' => $combo_max_per_page,
        ));
	}
	
	public function actionShow()
	{
		$combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];
		$max_per_page = Yii::app()->params['extjs_pager_max_per_page'];
	
		$form= new CampaignForm();
		$form->bindActiveRecord(Campaign::model());
	
		$this->render('/campaign/show.js', array(
				'form'       => $form,
				'max_per_page' => $max_per_page,
				'combo_max_per_page' => $combo_max_per_page,
		));
	}
	
	public function actionShowCampaignPriceUser()
	{
		$combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];
		$max_per_page = Yii::app()->params['extjs_pager_max_per_page'];
		
		$form= new CampaignPriceUserForm();
	
		$this->render('/campaign/showCampaignPriceUser.js', array(
				'form'       => $form,
				'max_per_page' => $max_per_page,
				'combo_max_per_page' => $combo_max_per_page,
		));
	}
	
	public function actionShowCampaignPriceGeneral()
	{
		$combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];
		$max_per_page = Yii::app()->params['extjs_pager_max_per_page'];
		
		
		$form= new CampaignPriceGeneralForm();
	
		$this->render('/campaign/showCampaignPriceGeneral.js', array(
				'form'       => $form,
				'max_per_page' => $max_per_page,
				'combo_max_per_page' => $combo_max_per_page,
		));
	}
	/**
	 * Kampanyok box - lista
	 */
	public function actionGetCampaignList()
	{
		$extra_params = array();
		$this->addPagerParams($extra_params);
		$this->addOrderParams($extra_params);
		$this->addFilterParams($extra_params);
		
		$onlyActive = $this->getParameter('onlyActive', 0, false);

		$response = CampaignManager::getInstance()->getCampaigns($extra_params, null, $onlyActive);
	
		$this->renderText(json_encode($response));
	}
	
	public function actionShowCampaignPriceRules()
	{
		$campaignId = $this->getParameter('id');
		
		$combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];
		$max_per_page = Yii::app()->params['extjs_pager_max_per_page'];
	
		CampaignManager::getInstance()->startPriceRulesForCampaign($campaignId);
	
		$this->render('/campaign/showCampaignPriceRules.js', array(
				'max_per_page' => $max_per_page,
				'combo_max_per_page' => $combo_max_per_page,
				'campaignId' => $campaignId,
		));
	}
	
	
	/**
	 * Kampanyok box - torles
	 * 
	 */
	public function actionDeleteCampaign(){
		$campaignId = $this->getParameter('campaign_id');
		
		$response = CampaignManager::getInstance()->deleteCampaign($campaignId);
		
		$this->renderText(json_encode($response));
	}
	
	public function actionGetRecordData() {
		$id = $this->getParameter('id');
	
		$record = Campaign::model()->findByPk($id);
	
		$form_name = 'CampaignForm';
		$form = new $form_name();
		//print_r($record); exit;
		$data = array(
			$form->generateName('id') => $record->id,
			$form->generateName('campaign_type_detail_id') => $record->campaign_type_detail_id,	
			$form->generateName('location_id') => $record->location_id,

			$form->generateName('start_datetime') => $record->start_datetime,
			$form->generateName('end_datetime') => $record->end_datetime,	
		);

		$this->renderText(json_encode(array('success'=>true, 'data'=>$data)));
	}
	
	public function actionGetLeafRecordData(){
		$id = $this->getParameter('id');
		
		$cpr = CampaignPriceRules::model()->findByPk($id);
		
		$isGeneral = is_null($cpr->link_id);
		
		$form_name = $isGeneral ? 'CampaignPriceGeneralForm' : 'CampaignPriceUserForm' ;
		
		$form = new $form_name();
		
		$data = array(
			$form->generateName('id') => $cpr->id,
			$form->generateName('link_id') => $cpr->link_id,
			$form->generateName('name') => $cpr->name,	
			$form->generateName('price') => $cpr->price,
			$form->generateName('percent') => $cpr->percent,
			$form->generateName('price_type') => $cpr->price_type,
		);

		$this->renderText(json_encode(array('success'=>true, 'data'=>$data)));
	}

	public function actionGetCampaignPriceRulesList(){
		$campaignId = $this->getParameter('campaignId');
		
		$response = CampaignManager::getInstance()->getCampaignPriceRulesList($campaignId);
		
		$this->renderText(json_encode($response));		
	}
	
	public function actionGetRightToComissionUserList(){
		$extra_params = array();
		$this->addPagerParams($extra_params);
		$this->addOrderParams($extra_params);
		$this->addFilterParams($extra_params);
	
		$response = CampaignManager::getInstance()->getRightToComissionUsers($extra_params);
	
		$this->renderText(json_encode($response));
	}
	
	public function actionSaveCampaign(){
		$active_from_datetime = $this->getParameter('mdt');
		$active_to_datetime = $this->getParameter('atdt');
	
		$form = new CampaignForm();
		$params = $this->getParameter($form->getNameFormat());
	
		$params['start_datetime'] = $active_from_datetime;
		$params['end_datetime'] = $active_to_datetime;
	
		$result = CampaignManager::getInstance()->save('Campaign', $params, 'CampaignForm');
	
		$this->renderText(json_encode($result));
	}
	
	public function actionSaveLeaf(){
		$form = new CampaignPriceUserForm();
		$params = $this->getParameter($form->getNameFormat());

		$response = CampaignManager::getInstance()->saveCampaignPriceUser($params);
		
		$this->renderText(json_encode($response));
	}
	
	public function actionSaveGeneralLeaf(){
		$form = new CampaignPriceGeneralForm();
		$params = $this->getParameter($form->getNameFormat());

		$response = CampaignManager::getInstance()->saveCampaignPriceGeneral($params);
		
		$this->renderText(json_encode($response));
	}
	
	public function actionDeletePriceRuleRecord(){
		$priceRuleId = $this->getParameter('price_rule_id');
		
		$cpr = CampaignPriceRules::model()->findByPk($priceRuleId);
		$cpr->deleteNode();
		
		$this->renderText(json_encode(array('success' => true, 'error'=>0)));
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