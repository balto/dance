// <script type="text/javascript">
<?php

$mdl = new ExtModule($this, array(
    "cacheable" => true,
    "title" => 'Jelenléti ív'
));


// member variables______________________________________________

// functions, event handlers (declarations) ________________

// event handlersF




// methods
$cmapaignMemberStore = $this->nameHelper[2]['name'] . 'Store';

$mdl->onPayDatachanged(Ext::fn("this.stores.get('".$cmapaignMemberStore."').load();"));
//$mdl->createMethod("openRecord(grid, record, action, row, col)");
$mdl->createMethod("addMomentRecord(btn, pressed)", "this.addMoment()");
$mdl->createMethod("addMoment()");

$mdl->createMethod('selectCampaign(grid, record)', "
	this.selectCampaignRecord(grid, record);
");
$mdl->createMethod("selectCampaignRecord(grid, record)");

$mdl->createMethod('selectCampaignMoment(grid, record)', "
	this.selectCampaignMomentRecord(grid, record);
");
$mdl->createMethod("selectCampaignMomentRecord(grid, record)");

$mdl->createMethod("addTicketRecord(btn, pressed)", "this.addTicket()");
$mdl->createMethod("addTicket()");

$mdl->createMethod("resetTicketForm(btn, pressed)", "this.resetTicket()");
$mdl->createMethod("resetTicket()");

$mdl->createMethod('deleteCampaignMoment(grid, record)', "
	this.deleteCampaignMomentRecord(grid, record);
");
$mdl->createMethod("deleteCampaignMomentRecord(grid, record)");

$mdl->createMethod("setCampaignMomentSelectedRowClass()");

$mdl->createMethod("addMember()");
$mdl->createMethod("showMemberInfo()");

$mdl->createMethod("openPayIn(grid, record, action, row, col)");
// models and stores ______________________________________


$mdl->createModel("Basic", $this->getBasicSelectFieldDefinitions());
$mdl->createModel("TicketTypeModel", $this->getTicketTypeSelectFieldDefinitions());

foreach ($this->nameHelper as $data) {
	//models
	$name     = $data['name'];
	$autoLoad = isset($data['autoLoad']) ? $data['autoLoad'] : false ;
	$module   = $data['module'];
	
	$modelName = $name . 'Data';
	$ldName    = 'list' . $name . 'FieldDefinitions';
	
	$extraParams = (isset($data['extraParams']) && !empty($data['extraParams'])) ? $data['extraParams'] : array() ;
	
	$mdl->createModel($modelName, call_user_func_array(array($this, $ldName), array()));
	
	//stores
	$storeName = $name . 'Store';
	$storeFunc = (isset($data['url'])) ? $data['url'] : 'get' . $name . 'List';
	$mdl->createStore($storeName)
		->model($mdl->model($modelName))
		->autoLoad($autoLoad)
		->pageSize($grid_max_per_page) // nem lehet megadni, hogy ne küldje a store a page paramétereket
		->remoteSort(true)
		->remoteFilter(true)
		->proxy(Ext::Proxy()
				->url($storeFunc, $this)
				->reader(Ext::JsonReader())
				->extraParams($extraParams)
		)
	;
	
	//$mdl->createMethod("open" . $name . "Record(grid, record, action, row, col)");
	//$mdl->createMethod("add" . $name . "Record(btn, pressed)", "theApp.showDialog('" . lcfirst($module) . "/" . lcfirst($name) . "/show', null, this);");
}


$mdl->createStore("ticketTypeComboStore")
	->model($mdl->model('TicketTypeModel'))
	->autoLoad(false)
	->remoteSort(false)
	->pageSize($combo_max_per_page) // nem lehet megadni, hogy ne küldje a store a page paramétereket
	->proxy(Ext::Proxy()->url('getTicketTypeList', $this)
		->reader(Ext::JsonReader())
		->extraParams(array('isCombo' => 1/*, 'campaignTypeId' => 1*/))
);

$mdl->createStore("memberComboStore")
	->model($mdl->model('Basic'))
	->autoLoad(false)
	->remoteSort(false)
	//->pageSize($combo_max_per_page) // nem lehet megadni, hogy ne küldje a store a page paramétereket
	->proxy(Ext::Proxy()->url('getMemberList', $this)
		->reader(Ext::JsonReader())
		->extraParams(array('isCombo' => 1))
);

$standardStores = array(
	'locationStore' => array('Location', true),
);

foreach ($standardStores as $storeName => $modelData) {
	$mdl->createStore($storeName)
	->model($mdl->model('Basic'))
	->autoLoad($modelData[1])
	->remoteSort(false)
	->pageSize($combo_max_per_page) // nem lehet megadni, hogy ne küldje a store a page paramétereket
	->proxy(Ext::Proxy()->url('getComboList', $this)
			->reader(Ext::JsonReader())
			->extraParams(array('model' => $modelData[0] , 'selection_field_name' => "CONCAT(name,' ' ,address)"))
	);
}

// view
$mdl->add(Ext::Container('attendanceSheetmainContainer')
	->layout('border')
);


$csrf_token = $ticketForm->generateCsrfToken();

$mdl->attendanceSheetmainContainer->add(
	Ext::Panel($this->nameHelper[0]['name'] . "Container")
	->collapsible(false)
	->title('Kampányok')
	->region('west')
	->layout('border')
	->flex(1)
	->add(
		Ext::GridPanel($this->nameHelper[0]['name'] . "Grid")
	    ->store($mdl->store($this->nameHelper[0]['name'] . "Store"))
	    ->preventHeader(true)
	    ->iconCls('icon-grid')
		->region('center')
	    ->bbar(Ext::PagingToolbar()
	        //->add(Ext::ToolbarSeparator())
        )
	    ->plugins(array(
	        new ExtCodeFragment("Ext.create('Ext.ux.grid.plugin.HeaderFilters', { pluginId: 'gridFilters', enableTooltip : false })")
	    ))
	    //->autoExpandColumn('name')
	    ->listeners(array(
	        'select'=> $mdl->selectCampaign,
	        'scope' => new ExtCodeFragment('this'),
	    ))
	)
	
);


$csrf_token = $campaignMomentForm->generateCsrfToken();

$momentForm = Ext::Form('CampaignMomentForm')
->border(false)
->defaults(array("labelWidth" => 170, "anchor" => "100%"))
->bodyPadding(5)
;

$momentForm->add(Ext::Hidden($campaignMomentForm->generateName($campaignMomentForm->getCSRFFieldname()))
		->value($csrf_token)
);

$momentForm->add(Ext::DateTimeField($campaignMomentForm->generateName('moment_datetime'))
		->fieldLabel($campaignMomentForm->getLabel('moment_datetime'))
		->allowBlank(false)
		->value('')
		->anchor("100%")
);


$momentForm->add(Ext::Button("ButtonNew" . $this->nameHelper[1]['name'] . "Record")
		->iconCls('icon-add')
		->text('Új alkalom')
		->handler($mdl->addMomentRecord)
);


$mdl->attendanceSheetmainContainer->add(
	Ext::Panel($this->nameHelper[1]['name'] . "Container")
	->collapsible(false)
	->title('Alkalmak')
	->region('center')
	->layout('border')
	->add(
		Ext::GridPanel($this->nameHelper[1]['name'] . "Grid")
			->store($mdl->store($this->nameHelper[1]['name'] . "Store"))
			->preventHeader(true)
			->iconCls('icon-grid')
			->region('center')
			->bbar(Ext::PagingToolbar()
			//		->add(Ext::ToolbarSeparator())
			)
			->plugins(array(
					new ExtCodeFragment("Ext.create('Ext.ux.grid.plugin.HeaderFilters', { pluginId: 'gridFilters', enableTooltip : false })")
			))
			->listeners(array(
					'select'=> $mdl->selectCampaignMoment,
					'scope' => new ExtCodeFragment('this'),
			))
			->rowaction(Ext::RowAction("remove")
					->iconCls('icon-remove')
					->qtip('Törlés')
					->callback($mdl->deleteCampaignMoment)
			)
			//->autoExpandColumn('name')
			/*->listeners(array(
					'itemdblclick'=> call_user_func_array(array($mdl, 'open' . $this->nameHelper[0]['name'] . 'Record'), array()),
					'scope' => new ExtCodeFragment('this'),
			))
	->rowaction(Ext::RowAction("edit")
			->iconCls('icon-edit-record')
			->qtip('Szerkesztés')
			->callback(call_user_func_array(array($mdl, 'open' . $this->nameHelper[0]['name'] . 'Record'), array()))
	)*/
	)
	->add(
		Ext::Panel($this->nameHelper[1]['name'] . "UnderContainer")
		->title(Yii::t('msg', 'Új alkalom indítása'))
		->region('south')
		->collapsible(true)
		->collapsed(true)
		->add($momentForm)
	)
);

$csrf_token = $ticketForm->generateCsrfToken();

$mdl->attendanceSheetmainContainer->add(
	Ext::Panel($this->nameHelper[2]['name'] . "Container")
	->collapsible(false)
	->title('Résztvevők')
	->region('east')
	->flex(1)
	->layout('border')
	->add(
		Ext::GridPanel($this->nameHelper[2]['name'] . "Grid")
			->store($mdl->store($this->nameHelper[2]['name'] . "Store"))
			->preventHeader(true)
			->iconCls('icon-grid')
			->region('center')
			->selModel(new ExtCodeFragment("Ext.create('Ext.selection.CheckboxModel', {checkOnly : true, showHeaderCheckbox : false })"))
			->bbar(Ext::PagingToolbar()
					//->add(Ext::ToolbarSeparator())
			)
			->plugins(array(
					new ExtCodeFragment("Ext.create('Ext.ux.grid.plugin.HeaderFilters', { pluginId: 'gridFilters', enableTooltip : false })")
			))
			->rowaction(Ext::RowAction("addMoney")
		        ->iconCls('icon-money_add')
		        ->qtip('Befizetés')
		        ->callback($mdl->openPayIn)
		    )
			//->autoExpandColumn('name')
			/*->listeners(array(
					'select'=> $mdl->selectCampaign,
					'scope' => new ExtCodeFragment('this'),
			))*/
			/*->rowaction(Ext::RowAction("edit")
			 ->iconCls('icon-edit-record')
					->qtip('Szerkesztés')
					->callback(call_user_func_array(array($mdl, 'open' . $this->nameHelper[0]['name'] . 'Record'), array()))
			)*/
	)
	->add(
		Ext::Panel($this->nameHelper[2]['name'] . "UnderContainer")
		->region('south')
		->title(Yii::t('msg', 'Új bérlet felvétele'))
		->collapsible(false)
		->collapsed(false)
		->add(
			Ext::Form('TicketForm')
			->border(false)
			->defaults(array("labelWidth" => 170, "anchor" => "100%"))
			->bodyPadding(5)
			->add(
				Ext::Hidden($ticketForm->generateName('csrf_token'))
				->value($csrf_token)
			)
			->add(
				Ext::FieldContainer('ticket_field_container')
				->layout('hbox')
				->fieldLabel($ticketForm->getLabel('member_id'))
				->add(
					Ext::ComboBox($ticketForm->generateName('member_id'))
					->store($mdl->store("memberComboStore"))
					->displayField('name')
					->hideTrigger(true)
					->typeAhead(true)
					->loadingText('loading...')
					->forceSelection(true)
					->valueField('id')
					->allowBlank(false)
					->flex(1)
				)
				->add(
					Ext::Button("AddMember" . $this->nameHelper[2]['name'])
					->iconCls('icon-add')
					->handler($mdl->addMember)
				)
				->add(
					Ext::Button("MemberInfo" . $this->nameHelper[2]['name'])
					->iconCls('icon-information')
					->handler($mdl->showMemberInfo)
				)
			)
			->add(
				Ext::ComboBox($ticketForm->generateName('ticket_type_id'))
				->store($mdl->store("ticketTypeComboStore"))
				->fieldLabel($ticketForm->getLabel('ticket_type_id'))
				->displayField('name')
				->valueField('id')
				->allowBlank(false)
				->anchor("100%")
			)
			->add(Ext::NumberField($ticketForm->generateName('price'))
		    	->value('')
				->allowBlank(false)
		    	->minValue(0)
		    	->fieldLabel($ticketForm->getLabel('price'))
		    )
			->add(Ext::NumberField($ticketForm->generateName('payed_price'))
		    	->value('')
				->allowBlank(false)
		    	->minValue(0)
		    	->fieldLabel($ticketForm->getLabel('payed_price'))
		    )
			->add(
				Ext::DateTimeField($ticketForm->generateName('active_from'))
				->fieldLabel($ticketForm->getLabel('active_from'))
				->allowBlank(false)
				->value('')
				->anchor("100%")
			)
			->add(
				Ext::DateTimeField($ticketForm->generateName('active_to'))
				->fieldLabel($ticketForm->getLabel('active_to'))
				->allowBlank(false)
				->value('')
				->anchor("100%")
			)
			->add(
				Ext::Container("ButtonContainer" . $this->nameHelper[2]['name'])
				->layout(array(
					'type' => 'hbox',
				))
				->add(
					Ext::Button("ButtonReset" . $this->nameHelper[2]['name'] . "Record")
					->iconCls('icon-add')
					->text(Yii::t('msg', 'Tisztítás'))
					->handler($mdl->resetTicketForm)
				)
				->add(
					Ext::ToolbarSpacer("Spacer1" . $this->nameHelper[2]['name'])
					->flex(1)	
				)
				->add(
					Ext::Button("ButtonNew" . $this->nameHelper[2]['name'] . "Record")
					->iconCls('icon-add')
					->text('Új bérlet')
					->handler($mdl->addTicketRecord)
					->flex(2)
				)
			)
		)
	)
);


// function implementation ________________________________

/**
 * Resztvevok box - uj berlet, uj member felvetele
 */
$mdl->addMember->begin(); ?>
theApp.showDialog('member/member/show', null, this);
<?php $mdl->addMember->end();

$mdl->showMemberInfo->begin(); ?>

var member = Ext.getCmp('<?php echo Ext::w($ticketForm->generateName('member_id'))->id; ?>');
var memberId = member.getValue();

if(memberId == null){
	Ext.Msg.alert('Hiba!', 'Válassz ki egy tagot!');
}
else{
	theApp.showDialog('member/member/showMemberInfo', {id : memberId}, this);
}

//theApp.showDialog('member/member/showMemberCampaignTypeInfo', null, this);
<?php $mdl->showMemberInfo->end();

/**
 * Alakalmak box - sor torlese
 */
$mdl->deleteCampaignMomentRecord->begin(); ?>
	var me = this;
	Ext.Msg.show({
		title: '<?php echo Yii::t('msg', "Kampány alkalom törlése") ?>',
		msg: '<?php echo Yii::t('msg', "Biztosan törli <b>{0}</b> kampány alkalmat?") ?>'.format(record.data.moment_datetime),
		buttons: Ext.Msg.YESNO,
		fn: function(buttonId) {
			if (buttonId=='yes') {
				Ext.Ajax.request({
					url: '<?php  echo ExtProxy::createUrl("deleteCampaignMoment", $this) ?>',
					method: 'POST',
					params: {
						campaign_moment_id: record.data.id
					},
					success: function(r) {
						var response = Ext.decode(r.responseText);
						if (response.success) {
							me.stores.get('<?php echo $this->nameHelper[1]['name'] . "Store"; ?>').load();
						} else {
							var errors = '<br /><br />';
							Ext.each(response.errors, function(error){
								errors += error+'<br />';
							});
							Ext.Msg.alert('Hiba!', response.message +errors);
						}						
					},
					failure: theApp.handleFailure
				});
			}
		},
		icon: Ext.MessageBox.QUESTION
	});
<?php $mdl->deleteCampaignMomentRecord->end();

/**
 * Alkalamak box - uj alkalom felvetele 
 */
$mdl->addMoment->begin(); ?>
	var campaignGrid = Ext.getCmp('<?php echo Ext::w($this->nameHelper[0]['name'] . "Grid")->id ?>');
	var campaignSelectionModel = campaignGrid.getSelectionModel();
	var campaignSelect = campaignSelectionModel.getSelection();

	var momentDateForm = Ext.getCmp('<?php echo Ext::w('CampaignMomentForm')->id; ?>');
	
	if(campaignSelect.length==1){
		var campaignId = campaignSelect[0].data.id;

		if(momentDateForm.getForm().isValid()){
			var momentDateTimeField = Ext.getCmp('<?php echo Ext::w($campaignMomentForm->generateName('moment_datetime'))->id; ?>');
			var mDT = momentDateTimeField.getRawValue();

			momentDateForm.getForm().submit({
                clientValidation: true,
                submitEmptyText: false,
                params : {campaign_id : campaignId, mdt: mDT},
                url: '<?php echo ExtProxy::createUrl('interface/attendanceSheet/saveMoment', $this) ?>',
                success: function(form, action) {
					var error = action.result.error;

					if(error.length){
						Ext.Msg.alert('Hiba!', action.result.error.join("\n"));
					}
					   
                	var campaignMomentGrid = Ext.getCmp('<?php echo Ext::w($this->nameHelper[1]['name'] . "Grid")->id ?>');
            	    campaignMomentGrid.getStore().load();

            	    momentDateForm.getForm().reset();
                    // ertesites az adatok megvaltozasarol
                    //me.parentWindow.fireEvent('datachanged');
                },
                failure: theApp.handleFormSubmitFailure,
                waitTitle: MESSAGES.SAVE_WAIT_TITLE,
                waitMsg: MESSAGES.SAVE_WAIT_MESSAGE
            });
		}
		
	}
	else{
		Ext.Msg.alert('Hiba', 'Válassz ki egy kampányt!');
	}

<?php $mdl->addMoment->end();

/**
 * Resztveveok box - uj berlet felvetele
 */
$mdl->addTicket->begin(); ?>
	var campaignGrid = Ext.getCmp('<?php echo Ext::w($this->nameHelper[0]['name'] . "Grid")->id ?>');
	var campaignSelectionModel = campaignGrid.getSelectionModel();
	var campaignSelect = campaignSelectionModel.getSelection();

	var campaignMomentGrid = Ext.getCmp('<?php echo Ext::w($this->nameHelper[1]['name'] . "Grid")->id ?>');
	var campaignMomentSelectionModel = campaignMomentGrid.getSelectionModel();
	var campaignMomentSelect = campaignMomentSelectionModel.getSelection();

	
	var ticketForm = Ext.getCmp('<?php echo Ext::w('TicketForm')->id; ?>');
	
	if(campaignMomentSelect.length==1){
		var campaignId = campaignSelect[0].data.id;
		var campaignMomentId = campaignMomentSelect[0].data.id;

		if(ticketForm.getForm().isValid()){
			var activeFromDateTimeField = Ext.getCmp('<?php echo Ext::w($ticketForm->generateName('active_from'))->id; ?>');
			var mDT = activeFromDateTimeField.getRawValue();

			var activeToDateTimeField = Ext.getCmp('<?php echo Ext::w($ticketForm->generateName('active_to'))->id; ?>');
			var atDT = activeToDateTimeField.getRawValue();

			ticketForm.getForm().submit({
                clientValidation: true,
                submitEmptyText: false,
                params : {campaign_id : campaignId, campaignMomentId : campaignMomentId, mdt: mDT, atdt: atDT},
                url: '<?php echo ExtProxy::createUrl('interface/attendanceSheet/saveTicket', $this) ?>',
                success: function(form, action) {
                	var message = action.result.message;
                	var resp_errors = action.result.errors;

    				if(typeof resp_errors != 'undefined' && resp_errors.length){
    					var errors = '<br /><br />';
    					Ext.each(resp_errors, function(error){
    						errors += error+'<br />';
    					});
    					Ext.Msg.alert('Hiba!', message +errors);
    				}
    				else{
    					var campaignMomentGrid = Ext.getCmp('<?php echo Ext::w($this->nameHelper[1]['name'] . "Grid")->id ?>');
                	    //campaignMomentGrid.getStore().load();

                	    var campaignMemberGrid = Ext.getCmp('<?php echo Ext::w($this->nameHelper[2]['name'] . "Grid")->id ?>');
                	    campaignMemberGrid.getStore().load();

                	    Ext.getCmp('<?php echo Ext::w($ticketForm->generateName('member_id'))->id ?>').reset();
        			}

    				

                },
                failure: theApp.handleFormSubmitFailure,
                waitTitle: MESSAGES.SAVE_WAIT_TITLE,
                waitMsg: MESSAGES.SAVE_WAIT_MESSAGE
            });
		}
		
	}
	else{
		Ext.Msg.alert('Hiba', 'Válassz ki egy kampány alkalmat!');
	}

<?php $mdl->addTicket->end();

$mdl->resetTicket->begin(); ?>
 var ticketForm = Ext.getCmp('<?php echo Ext::w('TicketForm')->id; ?>');
 ticketForm.getForm().reset();
 
<?php $mdl->resetTicket->end();

/**
 * Kampanyok box - ha kivalasztanak egy kampanyt
 */
$mdl->selectCampaignRecord->begin(); //(grid, record) ?>

    var campaignMomentGrid = Ext.getCmp('<?php echo Ext::w($this->nameHelper[1]['name'] . "Grid")->id ?>');
    campaignMomentGrid.getStore().load();

    var ticketTypeCombo = Ext.getCmp('<?php echo Ext::w($ticketForm->generateName('ticket_type_id'))->id ?>');
    ticketTypeCombo.getStore().load();

    var ticketForm = Ext.getCmp('<?php echo Ext::w('TicketForm')->id; ?>');

    Ext.Function.defer(function(tf){tf.getForm().reset();},300,this,[ticketForm]);
    
<?php $mdl->selectCampaignRecord->end();

/**
 * Alkalmak box - ha kivalasztanak egy alkalmat esemeny
 */
$mdl->selectCampaignMomentRecord->begin(); //(grid, record) ?>
    var campaignMemberGrid = Ext.getCmp('<?php echo Ext::w($this->nameHelper[2]['name'] . "Grid")->id ?>');
    campaignMemberGrid.getStore().load();

<?php $mdl->selectCampaignMomentRecord->end();

/**
 * Alkalmak box - a selected sorra rakja ra vagy veszi el a selected classt
 */
$mdl->setCampaignMomentSelectedRowClass->begin(); ?>
	var campaignMomentGrid = Ext.getCmp('<?php echo Ext::w($this->nameHelper[1]['name'] . "Grid")->id ?>');
	var campaignMomentSelectionModel = campaignMomentGrid.getSelectionModel();
	var campaignMomentSelect = campaignMomentSelectionModel.getSelection();

if(typeof campaignMomentSelect[0] != 'undefined'){
	
	var campaignMomentId = campaignMomentSelect[0].data.id;
	var trs = campaignMomentGrid.getView().el.dom.children[0].children[0].children;
	var campaignMomentStore = campaignMomentGrid.getStore();
	
	Ext.Array.each(campaignMomentStore.data.items, function(item, key){
		if(item.data.id == campaignMomentId){
			trs[key+1].className = 'x-grid-row x-grid-row-selected';
		}
		else{
			trs[key+1].className = 'x-grid-row';
		}
	});
}
<?php $mdl->setCampaignMomentSelectedRowClass->end();

$mdl->openPayIn->begin() //(grid, record, action, row, col) ?>

	theApp.showDialog('price/index/showPayIn', {
	    id: record.data.id,
	    name: record.data.member_name
	}, this);
<?php $mdl->openPayIn->end();


// template methods _______________________________________


$mdl->beginMethod("initModule()") ?>

Ext.getCmp('<?php echo Ext::w("TicketForm")->id ?>').getForm().reset();



var campaignGrid = Ext.getCmp('<?php echo Ext::w($this->nameHelper[0]['name'] . "Grid")->id ?>');
campaignGrid.updateHeader();

var campaignMomentGrid = Ext.getCmp('<?php echo Ext::w($this->nameHelper[1]['name'] . "Grid")->id ?>');
campaignMomentGrid.updateHeader();
campaignMomentGrid.getStore().removeAll();
campaignMomentGrid.getStore().getProxy().extraParams.campaign_id = 0;
var campaignMomentSelectionModel = campaignMomentGrid.getSelectionModel();

//campaignMomentSelectionModel.clearListeners('select');
//campaignMomentSelectionModel.clearListeners('deselect');

campaignMomentSelectionModel.on({
	'select' : {
		fn:function(obj,record,index){
			var select = <?php echo $mdl->setCampaignMomentSelectedRowClass; ?>

        	select();
		}
	},
	'deselect' : {
		fn:function(obj,record,index){
			var select = <?php echo $mdl->setCampaignMomentSelectedRowClass; ?>

        	select();
		}
	}
});


var campaignMemberGrid = Ext.getCmp('<?php echo Ext::w($this->nameHelper[2]['name'] . "Grid")->id ?>');
campaignMemberGrid.updateHeader();
var campaignMemberStore = campaignMemberGrid.getStore();
campaignMemberStore.removeAll();
campaignMemberStore.getProxy().extraParams.campaignMomentId = 0;
var campaignMemberSelectionModel = campaignMemberGrid.getSelectionModel();



campaignMemberSelectionModel.clearListeners('select');
campaignMemberSelectionModel.clearListeners('deselect');

campaignMemberSelectionModel.on({
	'select' : {
		fn:function(obj,record,index){
			var checked = (record.data.ticket_campaign_moment_id) ? 0 : 1 ;
			var campaignMemberGrid = Ext.getCmp('<?php echo Ext::w($this->nameHelper[2]['name'] . "Grid")->id ?>');
			var campaignMomentSelectionModel = campaignMomentGrid.getSelectionModel();
			var campaignMomentSelect = campaignMomentSelectionModel.getSelection();
			var campaignMomentId = campaignMomentSelect[0].data.id;
			
			var campaignMemberGridStore = campaignMemberGrid.getStore();
			
			var cm_record = campaignMemberGridStore.findRecord('id', record.data.id);

			Ext.Ajax.request({
	            url: '<?php echo ExtProxy::createUrl('interface/attendanceSheet/selectCampaignMember', $this) ?>',
	            params: {ticketId : record.data.id, campaignMomentId : campaignMomentId, checked : checked, isFree : cm_record.data.free, is_free : cm_record.data.is_free, is_main : cm_record.data.is_main},
	            scope: this,
	            success: function (result) {
	            	campaignMemberGrid.getStore().load();
	            	
	            	
	            },
	            failure: theApp.handleFailure
	        });
		}
	},
	'deselect' : {
		fn:function(obj,record,index){
			var checked = (record.data.ticket_campaign_moment_id) ? 0 : 1 ;
			var campaignMemberGrid = Ext.getCmp('<?php echo Ext::w($this->nameHelper[2]['name'] . "Grid")->id ?>');
			var campaignMomentSelectionModel = campaignMomentGrid.getSelectionModel();
			var campaignMomentSelect = campaignMomentSelectionModel.getSelection();
			var campaignMomentId = campaignMomentSelect[0].data.id;

			var cm_record = campaignMemberGridStore.findRecord('id', record.data.id);
						
			Ext.Ajax.request({
	            url: '<?php echo ExtProxy::createUrl('interface/attendanceSheet/selectCampaignMember', $this) ?>',
	            params: {ticketId : record.data.id, campaignMomentId : campaignMomentId, checked : checked, isFree : cm_record.data.free, is_free : cm_record.data.is_free, is_main : cm_record.data.is_main},
	            scope: this,
	            success: function (result) {
	            	campaignMemberGrid.getStore().load();

	            	
	            },
	            failure: theApp.handleFailure
	        });
		}
	}
});


var ticketTypeCombo = Ext.getCmp('<?php echo Ext::w($ticketForm->generateName('ticket_type_id'))->id ?>');
ticketTypeCombo.getStore().removeAll();
ticketTypeCombo.getStore().getProxy().extraParams.campaignTypeId = 0;

campaignMomentGrid.getStore().on(
	{
		'beforeload' : {fn:function(obj){
			var campaignGrid = Ext.getCmp('<?php echo Ext::w($this->nameHelper[0]['name'] . "Grid")->id ?>');
			var campaignSelectionModel = campaignGrid.getSelectionModel();
			var campaignSelect = campaignSelectionModel.getSelection();

			var campaignMemberGrid = Ext.getCmp('<?php echo Ext::w($this->nameHelper[2]['name'] . "Grid")->id ?>');
			campaignMemberGrid.getStore().removeAll();
			campaignMomentGrid.getStore().getProxy().extraParams.campaign_id = 0;
			
			if(campaignSelect.length==1){
				var campaignId = campaignSelect[0].data.id;
				obj.getProxy().extraParams.campaign_id = campaignId;
			}
			else{
				Ext.Msg.alert('Hiba', 'Válassz ki egy kampányt!');
			}

			
		}, scope: this}
	}
);

//campaignMemberGrid.getStore().clearListeners('beforeload');
//campaignMemberGrid.getStore().clearListeners('load');

campaignMemberGrid.getStore().on(
		{
			'beforeload' : {fn:function(obj){
				var campaignMomentSelectionModel = campaignMomentGrid.getSelectionModel();
				var campaignMomentSelect = campaignMomentSelectionModel.getSelection();

				if(campaignMomentSelect.length==1){
					var campaignMomentId = campaignMomentSelect[0].data.id;
					obj.getProxy().extraParams.campaignMomentId = campaignMomentId;
				}
				else{
					Ext.Msg.alert('Hiba', 'Válassz ki egy kampány alkalmat!');
				}

				
			}, scope: this},
			'load' : {
				fn: function(obj){
					var trs = campaignMemberGrid.getView().el.dom.children[0].children[0].children;
	                var campaignMemberStore = campaignMemberGrid.getStore();
	                
					Ext.Array.each(campaignMemberStore.data.items, function(item, key){
						if(item.data.ticket_campaign_moment_id){
							trs[key+1].className = 'x-grid-row x-grid-row-selected';
						}
						else{
							trs[key+1].className = 'x-grid-row';
						}
					});
				}
			}
		}
	);

//campaignTypeCombo.getStore().clearListeners('beforeload');

ticketTypeCombo.getStore().on(
		{
			'beforeload' : {fn:function(obj){
				var campaignGrid = Ext.getCmp('<?php echo Ext::w($this->nameHelper[0]['name'] . "Grid")->id ?>');
				var campaignSelectionModel = campaignGrid.getSelectionModel();
				var campaignSelect = campaignSelectionModel.getSelection();

				if(campaignSelect.length==1){
					var campaignTypeId = campaignSelect[0].data.campaign_type_id;
					obj.getProxy().extraParams.campaignTypeId = campaignTypeId;
				}
				else{
					Ext.Msg.alert('Hiba', 'Válassz ki egy kampányt!');
				}

				
			}, scope: this}
		}
	);

	//grid.getPlugin('gridFilters').clearFilters();

	var store = this.stores.get('<?php echo $this->nameHelper[0]['name'].'Store'; ?>');
	store.load({
		params:{
            start: 0,
            limit: <?php echo $max_per_page; ?>
        },
		scope: this,
		callback: function() {
			this.fireEvent('moduleready');
		}
	});
	this.callParent();
	return false;
	
<?php
$mdl->endMethod();

$mdl->render();

?>