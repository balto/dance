<?php

class MemberController extends Controller
{
	
	public $import = array(
			//'application.modules.campaign.models.CampaignType',
			'application.modules.campaign.components.CampaignManager',
			
			//'application.modules.campaign.models.CampaignForm',
			//'application.modules.ticket.models.TicketForm',
			//'application.modules.ticket.components.TicketManager',
	);
    public function actionIndex()
    {
        $combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];

        $this->render('/member/list.js', array(
            'max_per_page' => Yii::app()->params['extjs_pager_max_per_page'],
            'combo_max_per_page' => $combo_max_per_page,
        ));
    }

    /**
     * Itt definiáljuk a tagok (client) model lista oszlopainak beállításait.
     * A konfiguráció az ExtJS GridColumn config paramétereinek felel meg.
     *
     * @return array
     */
	
	public function listFieldDefinitions($require = false, $group = false)
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

	
	
		return $fields;
	}

	public function listMemberFieldDefinitions()
	{
		$ext = extjsHelper::ext_getHelper();
	
		$fields = array();
	
		$labels = Member::model()->attributeLabels();
	
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
						'filterName' => "name",
				)),
		);
	
		$fields[] = array(
				'header' => $labels['email'],
				'name' => 'email',
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
						'filterName' => "email",
				)),
		);
		
		$fields[] = array(
				'header' => $labels['birthdate'],
				'name' => 'birthdate',
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
						'filterName' => "birthdate",
				)),
		);
		
		$fields[] = array(
				'header' => $labels['address'],
				'name' => 'address',
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
						'filterName' => "address",
				)),
		);
		
		$fields[] = array(
				'header' => $labels['sex'],
				'name' => 'sex',
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
						'filterName' => "sex",
				)),
		);
	
	
		return $fields;
	}
	
	
	public function listMemberCampaignTypeFieldDefinitions()
	{
		$ext = extjsHelper::ext_getHelper();
	
		$fields = array();
	
		$labels = MemberCampaignType::model()->attributeLabels();
		
		
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
				'header' => 'Tánc típus',
				'name' => 'dance_type_name',
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
				'width' => 0,
				'values' => array(
				),
		);
	
		$fields[] = array(
				'header' => $labels['campaign_type_id'],
				'name' => 'campaign_type_name',
				'mapping' => '',
				'method' => '',
				'type' => 'string',
				'sortType' => '',
				'sortDir' => 'ASC',
				'dateFormat' => '',
				'defaultValue' => '',
				'resizable' => '',
				'align' => '',
				'renderer' => 'function(value,c,record){return record.data.dance_type_name + " "+value}',
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
				'header' => $labels['done'],
				'name' => 'done',
				'mapping' => '',
				'method' => '',
				'type' => 'string',
				'sortType' => '',
				'sortDir' => '',
				'dateFormat' => '',
				'defaultValue' => '',
				'resizable' => '',
				'align' => '',
				'renderer' => 'function(value,c,record){ return (value==1) ? "igen" : "nem" ;}',
				'groupable' => false,
				'gridColumn' => true,
				'flex' => 1,
				'values' => array(
				),
				'filter' => array(array("xtype" => "textfield",
					'filterName' => "mct.done",
				)),
		);
	
		$fields[] = array(
				'header' => $labels['start_date'],
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
				'flex' => 1,
				'values' => array(
				),
				'filter' => array(array("xtype" => "textfield",
					'filterName' => "mct.start_date",
				)),
		);
		
		$fields[] = array(
				'header' => $labels['end_date'],
				'name' => 'end_date',
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
					'filterName' => "mct.end_date",
				)),
		);
	
	
		return $fields;
	}
	
	/**
	 * Visszaadja az adott member meglevo kampany tipusait
	 */
	public function actionGetDoneCampaignTypeList()
	{
		$extra_params = array();
		$this->addPagerParams($extra_params);
		$this->addOrderParams($extra_params);
		$this->addFilterParams($extra_params);
	
		$memberId = $this->getParameter('memberId', 0, false);
	
		$response = CampaignManager::getInstance()->getDoneCampaignTypeToMember($memberId, $extra_params);
	
		$this->renderText(json_encode($response));
	}

    public function actionGetList()
    {
        $extra_params = array();
        $this->addPagerParams($extra_params);
        $this->addOrderParams($extra_params);
        $this->addFilterParams($extra_params);

        $response = MemberManager::getInstance()->getMembers($extra_params);

        $this->renderText(json_encode($response));
    }
    
    public function actionShowCampaignType()
    {
    	$combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];
    	$max_per_page = Yii::app()->params['extjs_pager_max_per_page'];
    
    	$this->render('/member/showCampaignTypes.js', array(
    			'max_per_page' => $max_per_page,
    			'combo_max_per_page' => $combo_max_per_page,
    	));
    }
/*
    public function actionGetHistoryList()
    {
        $id = $this->getParameter('id');

        $this->addFilter('client_id', $id);

        $extra_params = array();
        $this->addPagerParams($extra_params);
        $this->addOrderParams($extra_params);
        $this->addFilterParams($extra_params);

        $response = MemberManager::getInstance()->getMemberHistory($id, $extra_params);

        $this->renderText(json_encode($response));
    }
*/
    public function actionShow()
    {
        $combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];
        $max_per_page = Yii::app()->params['extjs_pager_max_per_page'];

        $form = new MemberForm();
        $form->bindActiveRecord(Member::model());

        $this->render('/member/show.js', array(
            'form' => $form,
            'max_per_page' => $max_per_page,
            'combo_max_per_page' => $combo_max_per_page,
        ));
    }
    
    public function actionGetRecordData() {
    	$id = $this->getParameter('id');
    
    	$record = Member::model()->findByPk($id);
    
    	$form_name = 'MemberForm';
    	$form = new $form_name();

    	$data = array(
    		$form->generateName('id') => $record->id,
    		$form->generateName('name') => $record->name,
    		$form->generateName('email') => $record->email,
    		$form->generateName('birthdate') => $record->birthdate,
    		$form->generateName('address') => $record->address,
    		$form->generateName('sex') => $record->sex,
    	);
    
    	$this->renderText(json_encode(array('success'=>true, 'data'=>$data)));
    }
   
    public function actionSave()
    {
    	$doneCampaignTypes = $this->getParameter('doneCampaignTypes', array(), false);
        $form = new MemberForm();
        $params = $this->getParameter($form->getNameFormat());
        

        $response = MemberManager::getInstance()->save($params);
        CampaignManager::getInstance()->saveCampaignTypesForMember($response['id'], $doneCampaignTypes);

        $this->renderText(json_encode($response));
    }
	
	public function actionShowMemberInfo()
    {
        $combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];
        $max_per_page = Yii::app()->params['extjs_pager_max_per_page'];

        $this->render('/member/showMemberInfo.js', array(
            'max_per_page' => $max_per_page,
            'combo_max_per_page' => $combo_max_per_page,
        ));
    }
	
	public function actionGetMemberCampaignTypeList() {
    	$id = $this->getParameter('id');
    
    	$extra_params = array();
        $this->addPagerParams($extra_params);
        $this->addOrderParams($extra_params);
        $this->addFilterParams($extra_params);

        $response = MemberManager::getInstance()->getMemberCampaignTypes($id, $extra_params);

        $this->renderText(json_encode($response));
    }
	
}