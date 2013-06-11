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
$dlg->createMethod("showCampaignTypeDetail()");

$dlg->createMethod("addRecord(btn, pressed)", "this.showReqSelect()");
$dlg->createMethod("removeRecord(grid, record, action, row, col)");

$dlg->createMethod("addDetailRecord(btn, pressed)", "this.showCampaignTypeDetail()");
$dlg->createMethod("removeDetailRecord(grid, record, action, row, col)");

// models and stores ______________________________________

$dlg->createModel("Basic", $this->getBasicSelectFieldDefinitions());
$dlg->createModel("CampaignTypeDetail", $this->campaignTypeDetaillistFieldDefinitions());
$dlg->createModel("RequiredCampaignType", $this->listFieldDefinitions(true));


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

$dlg->createStore("campaignTypeDetailGridStore")
	->model($dlg->model("CampaignTypeDetail"))
	->autoLoad(false)
	->remoteSort(true)
	->remoteFilter(true)
	->proxy(Ext::Proxy()
		->url("getCampaignTypeDetailList", $this)
		->reader(Ext::JsonReader())
	)
;

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

$dlg->window->width(500)->height(580);
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
    
    ->add(Ext::Hidden($form->generateName($form->getCSRFFieldname()))
		->value($csrf_token)
    )
    
    ->add(Ext::Hidden('helperId')
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
			)
		)
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

$dlg->add(Ext::GridPanel("DetailGrid")
		->store($dlg->store("campaignTypeDetailGridStore"))
		->preventHeader(true)
		->collapsible(true)
		->height(200)
		->title('Kampány típus részletek')
		->iconCls('icon-grid')
		->bbar(Ext::PagingToolbar()
				->add(Ext::ToolbarSeparator())
				->add(Ext::Button("ButtonNewDetailRecord")
						->iconCls('icon-add')
						->text('Új kampány típus részlet')
						->handler($dlg->addDetailRecord)
				))
		->plugins(array(
				new ExtCodeFragment("Ext.create('Ext.ux.grid.plugin.HeaderFilters', { pluginId: 'gridFilters', enableTooltip : false })")
		))
		->autoExpandColumn('name')
		->rowaction(Ext::RowAction("remove")
				->iconCls('icon-remove')
				->qtip('Törlés')
				->callback($dlg->removeDetailRecord)
		)
)
;

// function implementation ________________________________


$dlg->save->begin()?>
var me = this,
    form = Ext.getCmp('<?php echo $dlg->CampaignTypeForm->id ?>')
   ;

var grid = Ext.getCmp('<?php echo $dlg->Grid->id ?>');

var gridStore = grid.getStore();
var requiredCampaignTypes = [];

gridStore.each(function(record){
	var dataData = [record.data.id, record.data.campaign_type_group];
	requiredCampaignTypes.push(dataData);
});


var detailGrid = Ext.getCmp('<?php echo $dlg->DetailGrid->id ?>');

var detailGridStore = detailGrid.getStore();
var campaignTypeDetails = [];

if(detailGridStore.count()==0){
	Ext.Msg.alert('Hiba!', 'Legalább 1 alkalom felvétele kötelező!');
}
else{
	detailGridStore.each(function(record){
		var detailData = [record.data.id, record.data.moment_count, record.data.required_moment_count, record.data.required_moments];
		campaignTypeDetails.push(detailData);
	});
	
	form.getForm().submit({
	    clientValidation: true,
	    submitEmptyText: false,
	    url: '<?php echo ExtProxy::createUrl('save', $this) ?>',
	    params: {'requiredCampaignTypes[]' : requiredCampaignTypes, 'campaignTypeDetails[]' : campaignTypeDetails},
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
}


<?php $dlg->save->end();

$dlg->removeRecord->begin() // (grid, record, action, row, col) ?>
	grid.getStore().remove(record);
		
<?php $dlg->removeRecord->end();

$dlg->removeDetailRecord->begin() // (grid, record, action, row, col) ?>

if(typeof record.data.id != 'undefined'){
	Ext.Msg.show({
		title: '<?php echo Yii::t('msg', "Kampány típus fajta") ?>',
		msg: '<?php echo Yii::t('msg', "Biztosan törli?") ?>',
		buttons: Ext.Msg.YESNO,
		fn: function(buttonId) {
			if (buttonId=='yes') {
				Ext.Ajax.request({
					url: '<?php  echo ExtProxy::createUrl("deleteCampaignTypeDetail", $this) ?>',
					method: 'POST',
					params: {
						campaign_type_detail_id: record.data.id
					},
					success: function(r) {
						var response = Ext.decode(r.responseText);
						if (response.success) {
							grid.getStore().remove(record);
							
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
	
}
else{
	grid.getStore().remove(record);
}
	
<?php $dlg->removeDetailRecord->end();

$dlg->showReqSelect->begin()?>
	var gridId = '<?php echo $dlg->Grid->id;?>';
	var editId = Ext.getCmp('<?php echo $dlg->CampaignTypeForm->helperId->id ?>').getValue();
	
	theApp.showDialog('<?= $dlg->getDialogId('showCampaignType'); ?>', {id: editId, gridId: gridId}, this);
<?php $dlg->showReqSelect->end();


$dlg->showCampaignTypeDetail->begin()?>
	var gridId = '<?php echo $dlg->DetailGrid->id;?>';
	var editId = Ext.getCmp('<?php echo $dlg->CampaignTypeForm->helperId->id ?>').getValue();
	
	theApp.showDialog('<?= $dlg->getDialogId('showCampaignTypeDetail'); ?>', {id: editId, gridId: gridId}, this);
<?php $dlg->showCampaignTypeDetail->end();
// template methods _______________________________________

$dlg->beginMethod("initDialog()") ?>
Ext.getCmp('<?php echo Ext::w($form->generateName('dance_type_id'))->id; ?>').getStore().load();

this.window.setDisabled(true);


var me = this,
    form = Ext.getCmp('<?php echo $dlg->CampaignTypeForm->id ?>');
form.getForm().reset();

var requiredCampaignTypeGrid = Ext.getCmp('<?php echo $dlg->Grid->id ?>');
requiredCampaignTypeGrid.updateHeader();
requiredCampaignTypeGrid.getStore().removeAll();

var campaignTypeDetailsGrid = Ext.getCmp('<?php echo $dlg->DetailGrid->id ?>');
campaignTypeDetailsGrid.updateHeader();
campaignTypeDetailsGrid.getStore().removeAll();

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
			
			campaignTypeDetailsGrid.getStore().getProxy().extraParams.campaignTypeId = this.params.id;
			campaignTypeDetailsGrid.getStore().load();

			Ext.getCmp('<?php echo $dlg->CampaignTypeForm->helperId->id ?>').setValue(me.params.id);
			
		},
		failure: theApp.handleFormFailure
	});

} else {
    me.window.setDisabled(false);
}

this.callParent(arguments);

<?php $dlg->endMethod();


$dlg->beginMethod("onRender()"); // Ha már tényleg megjelent a dialógus a DOM-ban, akkor lehet KeyMap-et tenni rá ?>
var me = this;

this.callParent(arguments);
<?php $dlg->endMethod();


$dlg->render();