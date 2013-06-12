// <script type="text/javascript">
<?php

// module definition ______________________________________

$dlg = new ExtDialog($this, array(
    "cacheable" => true,
    "title" => 'Jegy típus adatok',
    'layout' => 'anchor',
));


// functions, event handlers (declarations) ________________

$csrf_token = $form->generateCsrfToken();

$dlg->createMethod("save()");
$dlg->createMethod("showReqSelect()");
$dlg->createMethod("showPermissionSelect()");

$dlg->createMethod("addRecord(btn, pressed)", "this.showReqSelect()");
$dlg->createMethod("removeRecord(grid, record, action, row, col)");

$dlg->createMethod("addPermissionRecord(btn, pressed)", "this.showPermissionSelect()");
$dlg->createMethod("removePermissionRecord(grid, record, action, row, col)");

// models and stores ______________________________________

$dlg->createModel("Basic", $this->getBasicSelectFieldDefinitions());
$dlg->createModel("JoinCampaignType", $this->campaignTypelistFieldDefinitions());
$dlg->createModel("PermissionCampaignType", $this->campaignTypelistFieldDefinitions(true, true));

$dlg->createStore("GridStore")
	->model($dlg->model("JoinCampaignType"))
	->autoLoad(false)
	->remoteSort(true)
	->remoteFilter(true)
	->proxy(Ext::Proxy()
		->url("getJoinCampaignTypeList", $this)
		->reader(Ext::JsonReader())
		->extraParams(array('isMain' => 1))
	)
;

$dlg->createStore("GridPermissionStore")
	->model($dlg->model("PermissionCampaignType"))
	->autoLoad(false)
	->remoteSort(true)
	->remoteFilter(true)
	->proxy(Ext::Proxy()
		->url("getJoinCampaignTypeList", $this)
		->reader(Ext::JsonReader())
		->extraParams(array('isMain' => 0))
	)
;

// view  __________________________________________________

$dlg->window->width(500)->height(600);
$dlg->window->buttons(array(
    Ext::Button()->text('Mentés')->handler($dlg->save),
    Ext::Button()->text('Bezár')->handler(new ExtFunction("this.window.close()"))->scope(new ExtCodeFragment("this")),
));


$dlg->add(Ext::Form('TicketTypeForm')
	->border(false)
	->defaults(array("labelWidth" => 170, "anchor" => "100%"))
	->bodyPadding(5)
	);

$dlg->TicketTypeForm
    ->add(
    	Ext::Hidden($form->generateName('id'))
    	->value('')
    )
    ->add(Ext::NumberField($form->generateName('moment_count'))
        ->fieldLabel($form->getLabel('moment_count'))
        ->value('')
    	->allowBlank(false)
    )
    
    ->add(Ext::NumberField($form->generateName('valid_days'))
    	->fieldLabel($form->getLabel('valid_days'))
    	->value('')
    	->allowBlank(true)
    )
    
    ->add(Ext::NumberField($form->generateName('default_price'))
    	->fieldLabel($form->getLabel('default_price'))
    	->value('')
    	->minValue(0)
    	->allowBlank(true)
    )

    /*
    ->add(Ext::TextField($form->generateName('is_daily'))
    		->fieldLabel($form->getLabel('is_daily'))
    		->value('')
    		->allowBlank(false)
    )
    */
    ->add(Ext::Hidden($form->generateName($form->getCSRFFieldname()))
    		->value($csrf_token)
    )
    
    ->add(Ext::Hidden('helperId')
    		->value('')
    )
    
    ->add(Ext::Hidden('helperJoinMomentsId')
    		->value('')
    )

;

$dlg->add(Ext::GridPanel("Grid")
		->store($dlg->store("GridStore"))
		->preventHeader(true)
		->collapsible(true)
		->height(200)
		->title('Új kapcsolódó kampány típus(ok)')
		->iconCls('icon-grid')
		->bbar(Ext::PagingToolbar()
			->add(Ext::ToolbarSeparator())
			->add(Ext::Button("ButtonNewRecord")
				->iconCls('icon-add')
				->text('Új kapcsolódó kampány típus')
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
			new ExtCodeFragment("Ext.create('Ext.ux.grid.plugin.HeaderFilters', { pluginId: 'gridFilters', enableTooltip : false })"),
			new ExtCodeFragment("Ext.create('Ext.grid.plugin.CellEditing', { clicksToEdit: 2})"),
		))
		->autoExpandColumn('name')
		->rowaction(Ext::RowAction("removePermission")
			->iconCls('icon-remove')
			->qtip('Törlés')
			->callback($dlg->removePermissionRecord)
		)
)
;

// function implementation ________________________________

$dlg->save->begin()?>
var me = this,
    form = Ext.getCmp('<?php echo $dlg->TicketTypeForm->id; ?>')
   ;

var grid = Ext.getCmp('<?php echo $dlg->Grid->id; ?>');

var gridStore = grid.getStore();
var JoinCampaignTypes = [];

gridStore.each(function(record){
	var dataData = [record.data.id];
	JoinCampaignTypes.push(dataData);
});

var gridPermission = Ext.getCmp('<?php echo $dlg->GridPermission->id; ?>');
var gridPermissionStore = gridPermission.getStore();
var permissionedCampaignTypes = [];

gridPermissionStore.each(function(record){
	var dataData = [record.data.id, record.data.is_free];
	permissionedCampaignTypes.push(dataData);
});

form.getForm().submit({
    clientValidation: true,
    submitEmptyText: false,
    url: '<?php echo ExtProxy::createUrl('save', $this) ?>',
    params: {'JoinCampaignTypes[]' : JoinCampaignTypes, 'permissionedCampaignTypes[]' : permissionedCampaignTypes},
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

$dlg->removeRecord->begin() // (grid, record, action, row, col) ?>
	grid.getStore().remove(record);
		
<?php $dlg->removeRecord->end();

$dlg->removePermissionRecord->begin() // (grid, record, action, row, col) ?>
	grid.getStore().remove(record);
		
<?php $dlg->removePermissionRecord->end();

$dlg->showReqSelect->begin() ?>
	var gridId = '<?php echo $dlg->Grid->id;?>';
	var editId = Ext.getCmp('<?php echo $dlg->TicketTypeForm->helperId->id ?>').getValue();
	
	theApp.showDialog('<?= $dlg->getDialogId('showCampaignType'); ?>', {id: editId, gridId: gridId}, this);
<?php $dlg->showReqSelect->end();

$dlg->showPermissionSelect->begin() ?>
	var gridId = '<?php echo $dlg->GridPermission->id ?>';
	var editId = Ext.getCmp('<?php echo $dlg->TicketTypeForm->helperId->id ?>').getValue();
	
	theApp.showDialog('<?= $dlg->getDialogId('showCampaignTypePermission'); ?>', {id: editId, gridId: gridId}, this);
<?php $dlg->showPermissionSelect->end();

// template methods _______________________________________

$dlg->beginMethod("initDialog()") ?>
this.window.setDisabled(true);


var me = this,
    form = Ext.getCmp('<?php echo $dlg->TicketTypeForm->id ?>');
form.getForm().reset();

var JoinCampaignTypeGrid = Ext.getCmp('<?php echo $dlg->Grid->id ?>');
JoinCampaignTypeGrid.updateHeader();
JoinCampaignTypeGrid.getStore().removeAll();

var permissionedCampaignTypeGrid = Ext.getCmp('<?php echo $dlg->GridPermission->id ?>');
permissionedCampaignTypeGrid.updateHeader();
permissionedCampaignTypeGrid.getStore().removeAll();

if (this.params.id) {
	form.load({
		url: '<?php echo ExtProxy::createUrl("ticket/ticketType/getRecordData") ?>',
		params:{
			id: this.params.id,
			'credentials[]': ['isSuperAdmin']
		},
		success: function(form, action) {
			me.window.setDisabled(false);
			JoinCampaignTypeGrid.getStore().getProxy().extraParams.TicketTypeId = this.params.id;
			JoinCampaignTypeGrid.getStore().load();
			
			permissionedCampaignTypeGrid.getStore().getProxy().extraParams.TicketTypeId = this.params.id;
			permissionedCampaignTypeGrid.getStore().load();

			Ext.getCmp('<?php echo $dlg->TicketTypeForm->helperId->id ?>').setValue(me.params.id);
			
		},
		failure: theApp.handleFormFailure
	});
}
else{
	this.window.setDisabled(false);
}

this.callParent(arguments);

<?php $dlg->endMethod();


$dlg->render();