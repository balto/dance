// <script type="text/javascript">
<?php

// module definition ______________________________________

$dlg = new ExtDialog($this, array(
    "cacheable" => true,
    "title" => 'Kampány típus adatok',
    'layout' => 'anchor',
));


// functions, event handlers (declarations) ________________

$csrf_token = $form->generateCsrfToken();

$dlg->createMethod("save()");
$dlg->createMethod("showReqSelect()");

$dlg->createMethod("onChangeMomentCount(field, newValue, oldValue)", "this.changeMomentCount(field, newValue, oldValue)");
$dlg->createMethod("changeMomentCount(field, newValue, oldValue)");

$dlg->createMethod("onChangeRequiredMomentCount(field, newValue, oldValue)", "this.changeRequiredMomentCount(field, newValue, oldValue)");
$dlg->createMethod("changeRequiredMomentCount(field, newValue, oldValue)");

$dlg->createMethod("addRecord(btn, pressed)", "this.showReqSelect()");
$dlg->createMethod("removeRecord(grid, record, action, row, col)");
/*
$dlg->createMethod("addPermissionRecord(btn, pressed)", "this.showPermSelect()");
$dlg->createMethod("removePermissionRecord(grid, record, action, row, col)");
*/
// models and stores ______________________________________

$dlg->createModel("Basic", $this->getBasicSelectFieldDefinitions());
$dlg->createModel("RequiredCampaignType", $this->listFieldDefinitions(true, true));
//$dlg->createModel("PermissionCampaignType", $this->listFieldDefinitions(true));

$dlg->createStore("GridStore")
	->model($dlg->model("RequiredCampaignType"))
	->autoLoad(false)
	->remoteSort(true)
	->remoteFilter(true)
	->proxy(Ext::Proxy()
		->url("getRequiredCampaignTypeList", $this)
		->reader(Ext::JsonReader())
	)
;
/*
$dlg->createStore("GridPermissionStore")
	->model($dlg->model("PermissionCampaignType"))
	->autoLoad(false)
	->remoteSort(true)
	->remoteFilter(true)
	->proxy(Ext::Proxy()
		->url("getPermissionedCampaignTypeList", $this)
		->reader(Ext::JsonReader())
	)
;*/

$standardStores = array(
	'danceTypeStore' => array('DanceType', true),
);

foreach ($standardStores as $storeName => $modelData) {
	$dlg->createStore($storeName)
	->model($dlg->model('Basic'))
	->autoLoad($modelData[1])
	->remoteSort(false)
	->pageSize($combo_max_per_page) // nem lehet megadni, hogy ne küldje a store a page paramétereket
	->proxy(Ext::Proxy()->url('getComboList', $this)
		->reader(Ext::JsonReader())
		->extraParams(array('model' => $modelData[0]))
	);
}
// view  __________________________________________________

$dlg->window->width(500)->height(750);
$dlg->window->buttons(array(
    Ext::Button()->text('Mentés')->handler($dlg->save),
    Ext::Button()->text('Bezár')->handler(new ExtFunction("this.window.close()"))->scope(new ExtCodeFragment("this")),
));


$dlg->add(Ext::Form('CampaignTypeForm')
	->border(false)
	->defaults(array("labelWidth" => 170, "anchor" => "100%"))
	->bodyPadding(5)
	);

$dlg->CampaignTypeForm
    ->add(
    	Ext::Hidden($form->generateName('id'))
    	->value('')
    )
    ->add(Ext::TextField($form->generateName('name'))
        ->fieldLabel($form->getLabel('name'))
        ->value('')
    	->allowBlank(false)
    )
    
    ->add(Ext::ComboBox($form->generateName('dance_type_id'))
    		->store($dlg->store("danceTypeStore"))
    		->fieldLabel($form->getLabel('dance_type_id'))
    		->displayField('name')
    		->valueField('id')
    		->allowBlank(false)
    )
    
    ->add(Ext::NumberField($form->generateName('moment_count'))
    		->fieldLabel($form->getLabel('moment_count'))
    		->value('')
    		->minValue(0)
    		->allowBlank(true)
	    	->listeners(array(
    			"change" => $dlg->onChangeMomentCount,
    			"scope" => new ExtCodeFragment("this")
	    	))
    )
    
    ->add(Ext::NumberField($form->generateName('required_moment_count'))
    	->value('')
    	->minValue(1)
    	->fieldLabel($form->getLabel('required_moment_count'))
    	->listeners(array(
    		"change" => $dlg->onChangeRequiredMomentCount,
    		"scope" => new ExtCodeFragment("this")
    	))
    )
    
    ->add(Ext::TextField($form->generateName('required_moments'))
    		->fieldLabel($form->getLabel('required_moments'))
    		->value('')
    )
	
	->add(Ext::Container($form->generateName('required_moments').'_description')
		->cls('field_description')
    	->html('<span>pl. : 1,2,4-6</span>')
    )
    
    ->add(Ext::Hidden($form->generateName($form->getCSRFFieldname()))
    		->value($csrf_token)
    )
    
    ->add(Ext::Hidden('helperId')
    		->value('')
    )
    
    ->add(Ext::Hidden('helperRequiredMomentsId')
    		->value('')
    )

;

$dlg->add(Ext::GridPanel("Grid")
		->store($dlg->store("GridStore"))
		->preventHeader(true)
		->collapsible(true)
		->height(200)
		->title('Kötelező kampány típus(ok)')
		->iconCls('icon-grid')
		->bbar(Ext::PagingToolbar()
				->add(Ext::ToolbarSeparator())
				->add(Ext::Button("ButtonNewRecord")
						->iconCls('icon-add')
						->text('Új kötelező kampány típus')
						->handler($dlg->addRecord)
				))
		->plugins(array(
				new ExtCodeFragment("Ext.create('Ext.ux.grid.plugin.HeaderFilters', { pluginId: 'gridFilters', enableTooltip : false })")
		))
		->autoExpandColumn('name')
		->rowaction(Ext::RowAction("remove")
				->iconCls('icon-remove')
				->qtip('Törlés')
				->callback($dlg->removeRecord)
		)
)
;
/*
$dlg->add(Ext::GridPanel("GridPermission")
		->store($dlg->store("GridPermissionStore"))
		->preventHeader(true)
		->collapsible(true)
		->height(200)
		->title('Jogosult kampány típus(ok)')
		->iconCls('icon-grid')
		->bbar(Ext::PagingToolbar()
				->add(Ext::ToolbarSeparator())
				->add(Ext::Button("ButtonPermissionNewRecord")
						->iconCls('icon-add')
						->text('Új jogosult kampány típus')
						->handler($dlg->addPermissionRecord)
				))
		->plugins(array(
				new ExtCodeFragment("Ext.create('Ext.ux.grid.plugin.HeaderFilters', { pluginId: 'gridFilters', enableTooltip : false })")
		))
		->autoExpandColumn('name')
		->rowaction(Ext::RowAction("removePermission")
				->iconCls('icon-remove')
				->qtip('Törlés')
				->callback($dlg->removePermissionRecord)
		)
)
;
*/

// function implementation ________________________________

$dlg->save->begin()?>
var me = this,
    form = Ext.getCmp('<?php echo $dlg->CampaignTypeForm->id ?>')
   ;

var grid = Ext.getCmp('<?php echo $dlg->Grid->id ?>');

var gridStore = grid.getStore();
var requiredCampaignTypes = [];

var gridPermission = Ext.getCmp('<?php echo $dlg->GridPermission->id ?>');
/*
var gridPermissionStore = gridPermission.getStore();
var permissionedCampaignTypes = [];
*/
gridStore.each(function(record){
	var dataData = [record.data.id, record.data.campaign_type_group];
	requiredCampaignTypes.push(dataData);
});
/*
gridPermissionStore.each(function(record){
	var dataData = [record.data.id];
	permissionedCampaignTypes.push(dataData);
});
*/
form.getForm().submit({
    clientValidation: true,
    submitEmptyText: false,
    url: '<?php echo ExtProxy::createUrl('save', $this) ?>',
    params: {'requiredCampaignTypes[]' : requiredCampaignTypes/*, 'permissionedCampaignTypes[]' : permissionedCampaignTypes*/},
    success: function(form, action) {
        me.changed = false;
        me.window.close();

        // ertesites az adatok megvaltozasarol
        me.parentWindow.fireEvent('datachanged', me.params.id);
    },
    failure: theApp.handleFormSubmitFailure,
    waitTitle: MESSAGES.SAVE_WAIT_TITLE,
    waitMsg: MESSAGES.SAVE_WAIT_MESSAGE
});
<?php $dlg->save->end();

$dlg->changeMomentCount->begin()?>
	var requiredMomentField = Ext.getCmp('<?php echo Ext::w($form->generateName('required_moment_count'))->id ?>');

	if(newValue!=null){
		requiredMomentField.setDisabled(false);
	}
	else{
		requiredMomentField.setDisabled(true);
		requiredMomentField.setValue('');
	}
<?php $dlg->changeMomentCount->end();


$dlg->changeRequiredMomentCount->begin()?>
	var reqMomentsField = Ext.getCmp('<?php echo Ext::w($form->generateName('required_moments'))->id ?>');

	if(newValue!=null){
		reqMomentsField.setDisabled(false);
	}
	else{
		reqMomentsField.setDisabled(true);
	}
<?php $dlg->changeRequiredMomentCount->end();

$dlg->removeRecord->begin() // (grid, record, action, row, col) ?>
	grid.getStore().remove(record);
		
<?php $dlg->removeRecord->end();


$dlg->showReqSelect->begin()?>
	var gridId = '<?php echo $dlg->Grid->id;?>';
	var editId = Ext.getCmp('<?php echo $dlg->CampaignTypeForm->helperId->id ?>').getValue();
	
	theApp.showDialog('<?= $dlg->getDialogId('showCampaignType'); ?>', {id: editId, gridId: gridId}, this);
<?php $dlg->showReqSelect->end();

$dlg->showPermSelect->begin()?>
	var gridId = '<?php echo $dlg->GridPermission->id;?>';
	var editId = Ext.getCmp('<?php echo $dlg->CampaignTypeForm->helperId->id ?>').getValue();
	
	theApp.showDialog('<?= $dlg->getDialogId('showCampaignTypePermission'); ?>', {id: editId, gridId: gridId}, this);
<?php $dlg->showPermSelect->end();

// template methods _______________________________________

$dlg->beginMethod("initDialog()") ?>

this.window.setDisabled(true);


var me = this,
    form = Ext.getCmp('<?php echo $dlg->CampaignTypeForm->id ?>');
form.getForm().reset();

var requiredCampaignTypeGrid = Ext.getCmp('<?php echo $dlg->Grid->id ?>');
requiredCampaignTypeGrid.updateHeader();
requiredCampaignTypeGrid.getStore().removeAll();


if (this.params.id) {
	form.load({
		url: '<?php echo ExtProxy::createUrl("campaign/campaignType/getRecordData") ?>',
		params:{
			id: this.params.id,
			'credentials[]': ['isSuperAdmin']
		},
		success: function(form, action) {
			me.window.setDisabled(false);
			requiredCampaignTypeGrid.getStore().getProxy().extraParams.campaignTypeId = this.params.id;
			requiredCampaignTypeGrid.getStore().load();

			Ext.getCmp('<?php echo $dlg->CampaignTypeForm->helperId->id ?>').setValue(me.params.id);
			
		},
		failure: theApp.handleFormFailure
	});

} else {
    me.window.setDisabled(false);
    var requiredMomentField = Ext.getCmp('<?php echo Ext::w($form->generateName('required_moment_count'))->id ?>');
    requiredMomentField.setDisabled(true);

    var reqMomentsField = Ext.getCmp('<?php echo Ext::w($form->generateName('required_moments'))->id ?>');
    reqMomentsField.setDisabled(true);
}

this.callParent(arguments);

<?php $dlg->endMethod();


$dlg->beginMethod("onRender()"); // Ha már tényleg megjelent a dialógus a DOM-ban, akkor lehet KeyMap-et tenni rá ?>
var me = this;

this.callParent(arguments);
<?php $dlg->endMethod();


$dlg->render();