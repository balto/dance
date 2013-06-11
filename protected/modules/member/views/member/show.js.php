// <script type="text/javascript">
<?php

// module definition ______________________________________

$dlg = new ExtDialog($this, array(
    "cacheable" => true,
    "title" => 'Tag adatok',
    'layout' => 'anchor',
));


// functions, event handlers (declarations) ________________

$csrf_token = $form->generateCsrfToken();

$dlg->createMethod("save()");
$dlg->createMethod("showReqSelect()");

$dlg->createMethod("addRecord(btn, pressed)", "this.showReqSelect()");
$dlg->createMethod("removeRecord(grid, record, action, row, col)");

// models and stores ______________________________________

$dlg->createModel("Basic", $this->getBasicSelectFieldDefinitions());
$dlg->createModel("DoneCampaignType", $this->listFieldDefinitions(true, true));

$dlg->createStore("GridStore")
	->model($dlg->model("DoneCampaignType"))
	->autoLoad(false)
	->remoteSort(true)
	->remoteFilter(true)
	->proxy(Ext::Proxy()
		->url("getDoneCampaignTypeList", $this)
		->reader(Ext::JsonReader())
	)
;
/*
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
}*/
// view  __________________________________________________

$dlg->window->width(500)->height(450);
$dlg->window->buttons(array(
    Ext::Button()->text('Mentés')->handler($dlg->save),
    Ext::Button()->text('Bezár')->handler(new ExtFunction("this.window.close()"))->scope(new ExtCodeFragment("this")),
));


$dlg->add(Ext::Form('MemberForm')
	->border(false)
	->defaults(array("labelWidth" => 170, "anchor" => "100%"))
	->bodyPadding(5)
	);

$dlg->MemberForm
    ->add(
    	Ext::Hidden($form->generateName('id'))
    	->value('')
    )
    ->add(Ext::TextField($form->generateName('name'))
        ->fieldLabel($form->getLabel('name'))
        ->value('')
    	->allowBlank(false)
    )
    
    ->add(Ext::TextField($form->generateName('email'))
    		->fieldLabel($form->getLabel('email'))
    		->value('')
    		->vtype('email')
    		->allowBlank(true)
    )
    
    ->add(Ext::NumberField($form->generateName('birthdate'))
    		->fieldLabel($form->getLabel('birthdate'))
    		->value('')
    		->minValue(1900)
    		->maxValue(date('Y'))
    		->allowBlank(true)
	    	/*->listeners(array(
    			"change" => $dlg->onChangeMomentCount,
    			"scope" => new ExtCodeFragment("this")
	    	))*/
    )
    
    ->add(Ext::TextField($form->generateName('address'))
    		->fieldLabel($form->getLabel('address'))
    		->value('')
    		->allowBlank(true)
    )
    
	->add(Ext::FieldContainer($form->generateName('sex').'_fieldcontainer')
    	->fieldLabel($form->getLabel('sex'))
    	->defaultType('radiofield')
		->items(array(
			array('boxLabel' => Yii::t('msg', 'Férfi'), 'name' => $form->generateName('sex'), 'inputValue' => Member::MALE_NAME, 'checked' => true),
			array('boxLabel' => Yii::t('msg', 'Nő'), 'name' => $form->generateName('sex'), 'inputValue' => Member::FEMALE_NAME),
		))
    	
    )
	
    ->add(Ext::Hidden($form->generateName($form->getCSRFFieldname()))
    		->value($csrf_token)
    )
    
    ->add(Ext::Hidden('helperId')
    		->value('')
    )
    
    ->add(Ext::Hidden('helperDoneMomentsId')
    		->value('')
    )

;

$dlg->add(Ext::GridPanel("Grid")
		->store($dlg->store("GridStore"))
		->preventHeader(true)
		->collapsible(true)
		->height(200)
		->title('Teljesített kampány típus(ok)')
		->iconCls('icon-grid')
		->bbar(Ext::PagingToolbar()
				->add(Ext::ToolbarSeparator())
				->add(Ext::Button("ButtonNewRecord")
						->iconCls('icon-add')
						->text('Új teljesített kampány típus')
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


// function implementation ________________________________

$dlg->save->begin()?>
var me = this,
    form = Ext.getCmp('<?php echo $dlg->MemberForm->id ?>')
   ;

var grid = Ext.getCmp('<?php echo $dlg->Grid->id ?>');

var gridStore = grid.getStore();
var doneCampaignTypes = [];

gridStore.each(function(record){
	var dataData = [record.data.id];
	doneCampaignTypes.push(dataData);
});

form.getForm().submit({
    clientValidation: true,
    submitEmptyText: false,
    url: '<?php echo ExtProxy::createUrl('save', $this) ?>',
    params: {'doneCampaignTypes[]' : doneCampaignTypes},
    success: function(form, action) {
        me.changed = false;
        me.window.close();

        // ertesites az adatok megvaltozasarol
        me.parentWindow.fireEvent('memberdatachanged', me.params.id);
    },
    failure: theApp.handleFormSubmitFailure,
    waitTitle: MESSAGES.SAVE_WAIT_TITLE,
    waitMsg: MESSAGES.SAVE_WAIT_MESSAGE
});
<?php $dlg->save->end();
/*
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
*/
$dlg->removeRecord->begin() // (grid, record, action, row, col) ?>
	grid.getStore().remove(record);
		
<?php $dlg->removeRecord->end();


$dlg->showReqSelect->begin()?>
	var gridId = '<?php echo $dlg->Grid->id;?>';
	var editId = Ext.getCmp('<?php echo $dlg->MemberForm->helperId->id ?>').getValue();
	
	theApp.showDialog('<?= $dlg->getDialogId('showCampaignType'); ?>', {id: editId, gridId: gridId}, this);
<?php $dlg->showReqSelect->end();

// template methods _______________________________________

$dlg->beginMethod("initDialog()") ?>
this.window.setDisabled(true);


var me = this,
    form = Ext.getCmp('<?php echo $dlg->MemberForm->id ?>');
form.getForm().reset();

var doneCampaignTypeGrid = Ext.getCmp('<?php echo $dlg->Grid->id ?>');
doneCampaignTypeGrid.updateHeader();
doneCampaignTypeGrid.getStore().removeAll();

if (this.params.id) {
	form.load({
		url: '<?php echo ExtProxy::createUrl("member/member/getRecordData") ?>',
		params:{
			id: this.params.id,
			'credentials[]': ['isSuperAdmin']
		},
		success: function(form, action) {
			me.window.setDisabled(false);
			doneCampaignTypeGrid.getStore().load({params : {memberId : this.params.id}});

			Ext.getCmp('<?php echo $dlg->MemberForm->helperId->id ?>').setValue(me.params.id);
			
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