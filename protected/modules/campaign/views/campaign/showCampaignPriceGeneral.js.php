// <script type="text/javascript">
<?php

// module definition ______________________________________

$dlg = new ExtDialog($this, array(
    "cacheable" => true,
    "title" => 'Általános',
    'layout' => 'anchor',
));


// functions, event handlers (declarations) ________________

$csrf_token = $form->generateCsrfToken();

$dlg->createMethod("addRecord(btn, pressed)", "this.addLeaf()");
$dlg->createMethod("addLeaf()");

// models and stores ______________________________________


// view  __________________________________________________

$dlg->window->width(500)->height(240);
$dlg->window->buttons(array(
    Ext::Button()->text('Mentés')->handler($dlg->addRecord),
    Ext::Button()->text('Bezár')->handler(new ExtFunction("this.window.close()"))->scope(new ExtCodeFragment("this")),
));


$dlg->add(Ext::Form('CampaignPriceGeneralForm')
	->border(false)
	->defaults(array("labelWidth" => 170, "anchor" => "100%"))
	->bodyPadding(5)
	);

$dlg->CampaignPriceGeneralForm
    		->add(
				Ext::Hidden($form->generateName('csrf_token'))
				->value($csrf_token)
			)
			->add(
		    	Ext::Hidden($form->generateName('id'))
		    	->value('')
		    )			
		    ->add(
		    	Ext::Hidden($form->generateName('tree_parent_id'))
		    	->value('')
		    )
			
		    ->add(Ext::TextField($form->generateName('name'))
		        ->fieldLabel($form->getLabel('name'))
		        ->value('')
		    	->allowBlank(false)
		    )
			
			->add(Ext::NumberField($form->generateName('price'))
		    	->value('')
		    	->fieldLabel($form->getLabel('price'))
		    	
		    )
			
			->add(Ext::NumberField($form->generateName('percent'))
		    	->value('')
		    	->fieldLabel($form->getLabel('percent'))
		    	
		    )
		    
			/*->add(Ext::FieldContainer($form->generateName('price_type').'_fieldcontainer')
		    	->fieldLabel($form->getLabel('price_type'))
		    	->defaultType('radiofield')
				->items(array(
					array('boxLabel' => Yii::t('msg', 'Bevétel'), 'name' => $form->generateName('price_type'), 'inputValue' => Campaign::INCOME_NAME, 'checked' => true),
					array('boxLabel' => Yii::t('msg', 'Kiadás'), 'name' => $form->generateName('price_type'), 'inputValue' => Campaign::EXPENSE_NAME),
				))
		    	
		    )*/
;


// function implementation ________________________________


$dlg->addLeaf->begin(); ?>
	var me = this;
	var form = Ext.getCmp('<?php echo Ext::w('CampaignPriceGeneralForm')->id; ?>');
	
	if(form.getForm().isValid()){
		form.getForm().submit({
	        clientValidation: true,
	        submitEmptyText: false,
	        params : {},
	        url: '<?php echo ExtProxy::createUrl('campaign/campaign/saveGeneralLeaf', $this) ?>',
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

<?php $dlg->addLeaf->end();


// template methods _______________________________________

$dlg->beginMethod("initDialog()") ?>

this.window.setDisabled(true);

var me = this,
    form = Ext.getCmp('<?php echo $dlg->CampaignPriceGeneralForm->id ?>');
	form.getForm().reset();
    
if(this.params.parent_id){
	Ext.getCmp('<?php echo Ext::w($form->generateName('tree_parent_id'))->id ?>').setValue(this.params.parent_id);	
}



if (this.params.id) {
	form.load({
		url: '<?php echo ExtProxy::createUrl("campaign/campaign/getLeafRecordData") ?>',
		params:{
			id: this.params.id,
			'credentials[]': ['isSuperAdmin']
		},
		success: function(form, action) {
			me.window.setDisabled(false);
			
		},
		failure: theApp.handleFormFailure
	});
}
else{
	Ext.Function.defer(function(f){
		Ext.getCmp('<?php echo Ext::w($form->generateName('id'))->id ?>').reset();
		Ext.getCmp('<?php echo Ext::w($form->generateName('name'))->id ?>').reset();
		Ext.getCmp('<?php echo Ext::w($form->generateName('price'))->id ?>').reset();
		Ext.getCmp('<?php echo Ext::w($form->generateName('percent'))->id ?>').reset();
	}, 400, this);
    me.window.setDisabled(false);
}


this.callParent(arguments);

<?php $dlg->endMethod();


$dlg->beginMethod("onRender()"); // Ha már tényleg megjelent a dialógus a DOM-ban, akkor lehet KeyMap-et tenni rá ?>
var me = this;

this.callParent(arguments);
<?php $dlg->endMethod();


$dlg->render();