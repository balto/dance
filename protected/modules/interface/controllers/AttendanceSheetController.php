<?php

class AttendanceSheetController extends Controller
{
	public $import = array(
		'application.modules.campaign.components.CampaignManager',
		'application.modules.campaign.controllers.CampaignController',
		'application.modules.campaign.models.CampaignMomentForm',
		'application.modules.campaign.models.CampaignForm',
		'application.modules.ticket.models.TicketForm',
		'application.modules.ticket.components.TicketManager',
	);
	
	public $nameHelper = array(
		array('name' => 'Campaign', 'autoLoad' => false, 'module' => 'Campaign', 'url' => 'campaign/campaign/getCampaignList', 'extraParams' => array('onlyActive' => 1)),
		array('name' => 'CampaignMoment', 'module' => 'Campaign'),
		array('name' => 'CampaignMember', 'module' => 'Campaign'),
	);
	
	public function actionIndex()
	{
		$combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];
		$grid_max_per_page = Yii::app()->params['extjs_pager_max_per_page'];
		
		$campaignMomentForm = new CampaignMomentForm();
		$campaignMomentForm->bindActiveRecord(CampaignMoment::model());
		
		/*$campaignForm = new CampaignForm();
		$campaignForm->bindActiveRecord(Campaign::model());*/
		
		$ticketForm = new TicketForm();
		$ticketForm->bindActiveRecord(Ticket::model());

        $this->render('/attendanceSheet/showAttendanceSheett.js', array(
            'max_per_page'       => Yii::app()->params['extjs_pager_max_per_page'],
            'combo_max_per_page' => $combo_max_per_page,
        	'grid_max_per_page'  => $grid_max_per_page,
        	'campaignMomentForm' => $campaignMomentForm,
        	//'campaignForm'       => $campaignForm,
        	'ticketForm'         => $ticketForm,
        ));
	}
	
	public function listCampaignFieldDefinitions() {
		$ct = new CampaignController('dummy');
		
		return $ct->listCampaignFieldDefinitions();
	}
	
	public function getTicketTypeSelectFieldDefinitions() {
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
			'name' => 'name',
			'type' => '',
			'header' => 'Megnevezés',
			'xtype' => '',
			'sortDir' => 'asc',
			'gridColumn' => true,
			'flex' => 1,
		);
		
		$fields[] = array(
			'name' => 'valid_days',
			'type' => '',
			'header' => 'Érvényes',
			'xtype' => '',
			'sortDir' => '',
			'gridColumn' => false,
			'width' => 0,
		);
	
		return $fields;
	}
	
	public function listCampaignMomentFieldDefinitions() {
		$ext = extjsHelper::ext_getHelper();
	
		$fields = array();
	
		$labels = CampaignMoment::model()->attributeLabels();
		
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
			'header' => $labels['moment_datetime'],
			'name' => 'moment_datetime',
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
				'filterName' => "cm.moment_datetime",
			)),
		);
	
		$fields[] = array(
			'header' => 'Résztvevők',
			'name' => 'member_count',
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
			/*'filter' => array(array("xtype" => "textfield",
					'filterName' => "l.name",
			)),*/
		);
		
		return $fields;
	}
	
	public function listCampaignMemberFieldDefinitions() {
		$ext = extjsHelper::ext_getHelper();
	
		$fields = array();
		
		$fields[] = array(
			'header' => 'Ticket Id',
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
			'header' => 'Free',
			'name' => 'free',
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
			'header' => 'Main',
			'name' => 'is_main',
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
		
		/*$fields[] = array(
			'header' => 'Free',
			'name' => 'is_free',
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
		);*/
		//required_moment_count
		$fields[] = array(
			'header' => 'Required Moment Count',
			'name' => 'required_moment_count',
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
			'header' => 'Moment Count',
			'name' => 'moment_count',
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
			'header' => 'ticket_campaign_moment_id',
			'name' => 'ticket_campaign_moment_id',
			'mapping' => '',
			'method' => '',
			'type' => '',
			'sortType' => '',
			'sortDir' => '',
			'dateFormat' => '',
			'defaultValue' => '',
			'resizable' => '',
			'align' => 'right',
			/*'renderer' => 'function(value,c,record){ var checked = (value) ? \'checked\' : \'\' ; return "<input onclick=\''.
				new ExtCodeFragment("
					console.log(this.checked);	
				").
				' \' type=\'checkbox\' "+checked+" />";}',*/
			'groupable' => false,
			'gridColumn' => false,
			'width' => 1,
			'values' => array(
			),
			
		);
		
		$fields[] = array(
			'header' => 'Név',
			'name' => 'member_name',
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
				'filterName' => "m.name",
			)),
		);
	
		$fields[] = array(
			'header' => 'Bérlet alk.',
			'name' => 'ticket_type_moment_count',
			'mapping' => '',
			'method' => '',
			'type' => 'string',
			'sortType' => '',
			'sortDir' => '',
			'dateFormat' => '',
			'defaultValue' => '',
			'resizable' => '',
			'align' => '',
			//'renderer' => 'function(value,c,record){ var desc = "-"; if(record.data.moment_count > 0){ desc = value+"/"+record.data.ticket_moment_left; }  return desc;}',
			'renderer' => 'function(value,c,record){ return value+"/"+record.data.ticket_moment_left;}',
			'groupable' => false,
			'gridColumn' => true,
			'flex' => 1,
			'values' => array(
			),
			/*'filter' => array(array("xtype" => "textfield",
					'filterName' => "l.name",
			)),*/
		);
		
		$fields[] = array(
			'header' => 'Résztvevők',
			'name' => 'ticket_moment_left',
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
			'width' => 0,
			'values' => array(
			),
			/*'filter' => array(array("xtype" => "textfield",
			 'filterName' => "l.name",
			)),*/
		);
		
		$fields[] = array(
			'header' => 'Telj. alk.',
			'name' => 'success_moment_count',
			'mapping' => '',
			'method' => '',
			'type' => 'string',
			'sortType' => '',
			'sortDir' => '',
			'dateFormat' => '',
			'defaultValue' => '',
			'resizable' => '',
			'align' => '',
			'renderer' => 'function(value,c,record){var cl = (value>=record.data.required_moment_count) ? "campaign_type_success" : "campaign_type_fail" ;  return "<span class=\'"+cl+"\'>"+record.data.required_moment_count+"/"+value+"</span>";}',
			'groupable' => false,
			'gridColumn' => true,
			'flex' => 1,
			'values' => array(
			),
		);
		
		$fields[] = array(
			'header' => 'Bérlet ára',
			'name' => 'ticket_price',
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
			'width' => 0,
			'values' => array(
			),
			/*'filter' => array(array("xtype" => "textfield",
			 'filterName' => "l.name",
			)),*/
		);
		
		$fields[] = array(
			'header' => 'Fizetve',
			'name' => 'payed_price',
			'mapping' => '',
			'method' => '',
			'type' => 'string',
			'sortType' => '',
			'sortDir' => '',
			'dateFormat' => '',
			'defaultValue' => '',
			'resizable' => '',
			'align' => '',
			'renderer' => 'function(value,c,record){var debit = parseInt(record.data.ticket_price)-parseInt(value); var cl = "ticket_payed"; var desc = "fizetve"; if(debit>0){cl="ticket_not_payed"; desc = "tartozik, "+debit;} return "<span class=\'"+cl+"\'>"+desc+"</span>"  }',
			'groupable' => false,
			'gridColumn' => true,
			'flex' => 1,
			'values' => array(
			),
			/*'filter' => array(array("xtype" => "textfield",
			 'filterName' => "l.name",
			)),*/
		);
		
		$fields[] = array(
			'header' => '',
			'name' => 'campaign_ticket',
			'mapping' => '',
			'method' => '',
			'type' => 'string',
			'sortType' => '',
			'sortDir' => '',
			'dateFormat' => '',
			'defaultValue' => '',
			'resizable' => '',
			'align' => '',
			'renderer' => 'function(value,c,record){ var ct_class = (value==1) ? "icon-bullet-green" : "icon-bullet-red" ; return "<div class=\""+ct_class+"\" >&nbsp</div>"}',
			'groupable' => false,
			'gridColumn' => true,
			'width' => 30,
			'values' => array(
			),
		);
		
		$fields[] = array(
			'header' => '',
			'name' => 'is_free',
			'mapping' => '',
			'method' => '',
			'type' => '',
			'sortType' => '',
			'sortDir' => '',
			'dateFormat' => '',
			'defaultValue' => '',
			'resizable' => '',
			'align' => 'right',
			'renderer' => 'function(value,c,record){ var ct_class = (value==1) ? "icon-bullet-green" : "icon-bullet-red" ; return "<div class=\""+ct_class+"\" >&nbsp</div>"}',
			'groupable' => false,
			'gridColumn' => true,
			'width' => 30,
			'values' => array(
			),
		);
		
		return $fields;
	}
	
	public function actionGetCampaignMomentList()
	{
		$extra_params = array();
		$this->addPagerParams($extra_params);
		$this->addOrderParams($extra_params);
		$this->addFilterParams($extra_params);
		
		$campaignId = $this->getParameter('campaign_id');
	
		$response = CampaignManager::getInstance()->getCampaignMoment($campaignId, $extra_params);
	
		$this->renderText(json_encode($response));
	}
	
	/**
	 * Résztvevők box - lista
	 */
	public function actionGetCampaignMemberList()
	{
		$campaignMomentId = $this->getParameter('campaignMomentId');
		
		$extra_params = array();
		$this->addPagerParams($extra_params);
		$this->addOrderParams($extra_params);
		$this->addFilterParams($extra_params);

		$response = CampaignManager::getInstance()->getCampaignMember($campaignMomentId, $extra_params);
	
		$this->renderText(json_encode($response));
	}
	
	/**
	 * Alkalmak box - uj alkalom felvetele form mentes
	 */
	public function actionSaveMoment(){
		$campaign_id = $this->getParameter('campaign_id');
		$moment_datetime = $this->getParameter('mdt');

		$result = CampaignManager::getInstance()->saveCampaignMoment($campaign_id, $moment_datetime);
		
		$this->renderText(json_encode(array('success' => true, 'error' => $result)));
	}
	
	public function actionGetTicketTypeList()
	{
		$campaignTypeId = $this->getParameter('campaignTypeId');
	
		$extra_params = array();
		$this->addPagerParams($extra_params);
		$this->addOrderParams($extra_params);
		$this->addFilterParams($extra_params);
	
		$response = InterfaceManager::getInstance()->getTicketTypesForCampaignTypes($campaignTypeId, $extra_params);

		$this->renderText(json_encode($response));
	}
	
	public function actionGetMemberList()
	{
		$queryStr = $this->getParameter('query');
	
		$extra_params = array();
		$this->addPagerParams($extra_params);
		$this->addOrderParams($extra_params);
		$this->addFilterParams($extra_params);
	
		$response = InterfaceManager::getInstance()->getMembers($queryStr, $extra_params);
	
		$this->renderText(json_encode($response));
	}
	
	/**
	 * Resztvevok box - uj berlet mentese
	 */
	public function actionSaveTicket(){
		$campaign_id = $this->getParameter('campaign_id');
		$campaign_moment_id = $this->getParameter('campaignMomentId');
		$active_from_datetime = $this->getParameter('mdt');
		$active_to_datetime = $this->getParameter('atdt');
		
		$form = new TicketForm();
		$params = $this->getParameter($form->getNameFormat());
		
		$params['active_from'] = $active_from_datetime;
		$params['active_to'] = $active_to_datetime;
		
		$campaignTypeId = Campaign::model()->findByPk($campaign_id)->campaignTypeDetail->campaign_type_id;
		
		$memberId = $params['member_id'];
		
		$hasCampaignTypes = CampaignManager::getInstance()->hasRequiredCampaignTypeForNewCampaignType($memberId, $campaignTypeId);

		if(is_array($hasCampaignTypes)){
			$result = array(
				'success'=>true,
				'message'=>'Hiányzó kampány típusok: ',
				'errors' =>$hasCampaignTypes
			);
		}
		elseif (CampaignManager::getInstance()->hasTicketCampaignToMember($campaign_id, $memberId)){
			$result = array(
				'success'=>true,
				'message'=>'',
				'errors' => array('Már van érvényes bérlete erre a kampányra!')
			);
		}
		else{
		
			$result = TicketManager::getInstance()->save($params);
			
			if ($result['success']){
				CampaignManager::getInstance()->addTicketToCampaignMoment($result['id'], $campaign_moment_id, $active_from_datetime);
				CampaignManager::getInstance()->addTicketToCampaign($campaign_id, $result['id']);
			}
		
		}

		$this->renderText(json_encode($result));
	}
	
	/**
	 * Kampany box - uj kampany mentese
	 */
	
	
	/**
	 * Resztvevok box - pipa
	 * 
	 */
	public function actionSelectCampaignMember()
	{
		$campaignMomentId = $this->getParameter('campaignMomentId');
		$ticketId = $this->getParameter('ticketId');
		$checked = $this->getParameter('checked');
		$isFree = $this->getParameter('isFree');
		$is_free = $this->getParameter('is_free');
	
		CampaignManager::getInstance()->handleCampaignMomentCheck($ticketId, $campaignMomentId, $checked, null, $is_free);
	
		$this->renderText(json_encode(array('success' => true)));
	}
	
	
	
	/**
	 * Alkalmak box - torles
	 */
	public function actionDeleteCampaignMoment(){
		$campaignMomentId = $this->getParameter('campaign_moment_id');
	
		$response = CampaignManager::getInstance()->deleteCampaignMoment($campaignMomentId);
	
		$this->renderText(json_encode($response));
	}
/*	
	public function actionShowNewCampaignMoment()
	{
		$combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];
		$max_per_page = Yii::app()->params['extjs_pager_max_per_page'];
	
		$form = new CampaignMomentForm();
		$form->bindActiveRecord(CampaignMoment::model());
	
		$this->render('/attendanceSheet/showNewCampaignMoment.js', array(
				'form' => $form,
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
	
	public function actionEmptyTickets() {
		Yii::app()->db->createCommand('TRUNCATE TABLE campaign_ticket')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE member_campaign_type')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE ticket')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE ticket_campaign_moment')->execute();
	}
	
	public function actionEmptyCampaignsAndTickets() {
		Yii::app()->db->createCommand('TRUNCATE TABLE campaign')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE campaign_moment')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE campaign_price_rules')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE campaign_ticket')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE member_campaign_type')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE ticket')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE ticket_campaign_moment')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE ticket_type')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE ticket_type_campaign_type')->execute();
	}
	
	public function actionEmptyAll() {
		Yii::app()->db->createCommand('TRUNCATE TABLE campaign')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE campaign_moment')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE campaign_price_rules')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE campaign_ticket')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE campaign_type')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE campaign_type_detail')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE campaign_type_detail_moment')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE campaign_type_require')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE member_campaign_type')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE ticket')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE ticket_campaign_moment')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE ticket_type')->execute();
		Yii::app()->db->createCommand('TRUNCATE TABLE ticket_type_campaign_type')->execute();
	}
}