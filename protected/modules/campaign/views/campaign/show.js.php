// <script type="text/javascript">
<?php

// module definition ______________________________________

$dlg = new ExtDialog($this, array(
    "cacheable" => true,
    "title" => 'Kampány adatok',
    'layout' => 'anchor',
));


// functions, event handlers (declarations) ________________

$csrf_token = $form->generateCsrfToken();

$dlg->createMethod("addCampaignRecord(btn, pressed)", "this.addCampaign()");
$dlg->createMethod("addCampaign()");

// models and stores ______________________________________

$dlg->createModel("Basic", $this->getBasicSelectFieldDefinitions());


$dlg->createStore("CampaignCampaignTypeComboStore")
	->model($dlg->model('Basic'))
	->autoLoad(false)
	->remoteSort(false)
	->pageSize($combo_max_per_page) // nem lehet megadni, hogy ne küldje a store a page paramétereket
	->proxy(Ext::Proxy()->url('campaign/campaignTypeDetail/getList', $this)
		->reader(Ext::JsonReader())
		->extraParams(array('isCombo' => 1))
);

$standardStores = array(
	'locationStore' => array('Location', true),
);

foreach ($standardStores as $storeName => $modelData) {
	$dlg->createStore($storeName)
	->model($dlg->model('Basic'))
	->autoLoad($modelData[1])
	->remoteSort(false)
	->pageSize($combo_max_per_page) // nem lehet megadni, hogy ne küldje a store a page paramétereket
	->proxy(Ext::Proxy()->url('getComboList', $this)
			->reader(Ext::JsonReader())
			->extraParams(array('model' => $modelData[0] , 'selection_field_name' => "CONCAT(name,' ' ,address)"))
	);
}
// view  __________________________________________________

$dlg->window->width(500)->height(240);
$dlg->window->buttons(array(
    Ext::Button()->text('Mentés')->handler($dlg->addCampaignRecord),
    Ext::Button()->text('Bezár')->handler(new ExtFunction("this.window.close()"))->scope(new ExtCodeFragment("this")),
));


$dlg->add(Ext::Form('CampaignForm')
	->border(false)
	->defaults(array("labelWidth" => 170, "anchor" => "100%"))
	->bodyPadding(5)
	);

$dlg->CampaignForm
    		->add(
				Ext::Hidden($form->generateName('csrf_token'))
				->value($csrf_token)
			)
			->add(
		    	Ext::Hidden($form->generateName('id'))
		    	->value('')
		    )
			->add(
				Ext::ComboBox($form->generateName('campaign_type_detail_id'))
				->store($dlg->store("CampaignCampaignTypeComboStore"))
				->fieldLabel($form->getLabel('campaign_type_detail_id'))
				->displayField('name')
				->valueField('id')
				->allowBlank(false)
			)
			->add(
				Ext::ComboBox($form->generateName('location_id'))
				->store($dlg->store("locationStore"))
				->fieldLabel($form->getLabel('location_id'))
				->displayField('name')
				->valueField('id')
				->allowBlank(false)
			)
			->add(
				Ext::DateTimeField($form->generateName('start_datetime'))
				->fieldLabel($form->getLabel('start_datetime'))
				->allowBlank(false)
				->value('')
				->anchor("100%")
			)
			->add(
				Ext::DateTimeField($form->generateName('end_datetime'))
				->fieldLabel($form->getLabel('end_datetime'))
				->allowBlank(true)
				->value('')
				->anchor("100%")
			)
;


// function implementation ________________________________


$dlg->addCampaign->begin(); ?>
	var me = this;
	var campaignForm = Ext.getCmp('<?php echo Ext::w('CampaignForm')->id; ?>');
	
	if(campaignForm.getForm().isValid()){
		var startDateTimeField = Ext.getCmp('<?php echo Ext::w($form->generateName('start_datetime'))->id; ?>');
		var mDT = startDateTimeField.getRawValue();
	
		var endDateTimeField = Ext.getCmp('<?php echo Ext::w($form->generateName('end_datetime'))->id; ?>');
		var atDT = endDateTimeField.getRawValue();
	
		campaignForm.getForm().submit({
	        clientValidation: true,
	        submitEmptyText: false,
	        params : {mdt: mDT, atdt: atDT},
	        url: '<?php echo ExtProxy::createUrl('saveCampaign', $this) ?>',
	        success: function(form, action) {
	        	var message = action.result.message;
				var error = action.result.error;

				if(error.length){
					Ext.Msg.alert('Hiba!', action.result.error.join("\n"));
				}
				else{
					me.window.close();
		            // ertesites az adatok megvaltozasarol
	            	me.parentWindow.fireEvent('datachanged');
				}


	        },
	        failure: theApp.handleFormSubmitFailure,
	        waitTitle: MESSAGES.SAVE_WAIT_TITLE,
	        waitMsg: MESSAGES.SAVE_WAIT_MESSAGE
	    });
	}

<?php $dlg->addCampaign->end();


// template methods _______________________________________

$dlg->beginMethod("initDialog()") ?>
Ext.getCmp('<?php echo Ext::w($form->generateName('campaign_type_detail_id'))->id; ?>').getStore().load();
Ext.getCmp('<?php echo Ext::w($form->generateName('location_id'))->id; ?>').getStore().load();

this.window.setDisabled(true);

var me = this,
    form = Ext.getCmp('<?php echo $dlg->CampaignForm->id ?>');

   


if (this.params.id) {
	form.load({
		url: '<?php echo ExtProxy::createUrl("campaign/campaign/getRecordData") ?>',
		params:{
			id: this.params.id,
			'credentials[]': ['isSuperAdmin']
		},
		success: function(form, action) {
			me.window.setDisabled(false);
			
		},
		failure: theApp.handleFormFailure
	});

} else {
	Ext.Function.defer(function(f){f.getForm().reset();}, 400, this, [form]);
    me.window.setDisabled(false);
}


this.callParent(arguments);

<?php $dlg->endMethod();


$dlg->beginMethod("onRender()"); // Ha már tényleg megjelent a dialógus a DOM-ban, akkor lehet KeyMap-et tenni rá ?>
var me = this;

this.callParent(arguments);
<?php $dlg->endMethod();


$dlg->render();