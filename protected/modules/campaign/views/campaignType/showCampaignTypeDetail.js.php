// <script type="text/javascript">
<?php

// module definition ______________________________________

$dlg = new ExtDialog($this, array(
    "cacheable" => true,
    "title" => 'Kampány típus részlet',
    'layout' => 'anchor',
));


// functions, event handlers (declarations) ________________

$dlg->createMethod("done()");

$dlg->createMethod("onChangeMomentCount(field, newValue, oldValue)", "this.changeMomentCount(field, newValue, oldValue)");
$dlg->createMethod("changeMomentCount(field, newValue, oldValue)");

$dlg->createMethod("onChangeRequiredMomentCount(field, newValue, oldValue)", "this.changeRequiredMomentCount(field, newValue, oldValue)");
$dlg->createMethod("changeRequiredMomentCount(field, newValue, oldValue)");

// models and stores ______________________________________

// view  __________________________________________________

$dlg->window->width(400)->height(240);
$dlg->window->buttons(array(
    Ext::Button()->text('Ok')->handler($dlg->done),
    Ext::Button()->text('Bezár')->handler(new ExtFunction("this.window.close()"))->scope(new ExtCodeFragment("this")),
));

$dlg->add(Ext::Form('Form')
		->border(false)
		->defaults(array("labelWidth" => 170, "anchor" => "100%"))
		->bodyPadding(5)
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
);


$dlg->add(Ext::Form('HelperForm')
		->border(false)
		->defaults(array("labelWidth" => 170, "anchor" => "100%"))
		->bodyPadding(5)
		->add(
			Ext::Hidden('helperInput')
			->value('')
		)
);


// function implementation ________________________________

$dlg->done->begin()?>
var me = this;

	var helper = Ext.getCmp('<?php echo $dlg->HelperForm->helperInput->id; ?>');
	var parentGrid = Ext.getCmp(helper.getValue());
    
	var ctForm = Ext.getCmp('<?php echo $dlg->Form->id; ?>');
	var form = ctForm.getForm();
	
	if(form.isValid()){
		var requiredMomentField = Ext.getCmp('<?php echo Ext::w($form->generateName('required_moment_count'))->id ?>');
		var reqMomentsField = Ext.getCmp('<?php echo Ext::w($form->generateName('required_moments'))->id ?>');
		var momentCountField = Ext.getCmp('<?php echo Ext::w($form->generateName('moment_count'))->id ?>');
		
		parentGrid.getStore().add({moment_count : momentCountField.getValue(), required_moment_count : requiredMomentField.getValue(), required_moments : reqMomentsField.getValue()});
	
		var form = Ext.getCmp('<?php echo $dlg->Form->id ?>');
		form.getForm().reset();
	   	//me.window.close();
	}

<?php $dlg->done->end();


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
// template methods _______________________________________

$dlg->beginMethod("initDialog()") ?>
    
var me = this;

var requiredMomentField = Ext.getCmp('<?php echo Ext::w($form->generateName('required_moment_count'))->id ?>');
requiredMomentField.setDisabled(true);

var reqMomentsField = Ext.getCmp('<?php echo Ext::w($form->generateName('required_moments'))->id ?>');
reqMomentsField.setDisabled(true);

var form = Ext.getCmp('<?php echo $dlg->Form->id ?>');
form.getForm().reset();

var helper = Ext.getCmp('<?php echo $dlg->HelperForm->helperInput->id ?>');
helper.setValue(this.params.gridId);

this.callParent(arguments);

<?php $dlg->endMethod();


$dlg->beginMethod("onRender()"); // Ha már tényleg megjelent a dialógus a DOM-ban, akkor lehet KeyMap-et tenni rá ?>
var me = this;

this.callParent(arguments);
<?php $dlg->endMethod();


$dlg->render();
