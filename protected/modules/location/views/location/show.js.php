// <script type="text/javascript">
<?php

// module definition ______________________________________

$dlg = new ExtDialog($this, array(
    "cacheable" => true,
    "title" => 'Hely adatok',
    'layout' => 'anchor',
));


// functions, event handlers (declarations) ________________

$csrf_token = $form->generateCsrfToken();

$dlg->createMethod("save()");


// models and stores ______________________________________

// view  __________________________________________________

$dlg->window->width(400)->height(180);
$dlg->window->buttons(array(
    Ext::Button()->text('Mentés')->handler($dlg->save),
    Ext::Button()->text('Bezár')->handler(new ExtFunction("this.window.close()"))->scope(new ExtCodeFragment("this")),
));


$dlg->add(Ext::Form('LocationForm')
	->border(false)
	->defaults(array("labelWidth" => 120, "anchor" => "100%"))
	->bodyPadding(5)
	);

$dlg->LocationForm
    ->add(
    	Ext::Hidden($form->generateName('id'))
    	->value('')
    )
    ->add(Ext::TextField($form->generateName('name'))
        ->fieldLabel($form->getLabel('name'))
        ->value('')
    	->allowBlank(false)
    )
    
    ->add(Ext::TextField($form->generateName('address'))
    		->fieldLabel($form->getLabel('address'))
    		->value('')
    		->allowBlank(false)
    )
    
    ->add(Ext::Checkbox($form->generateName('is_active'))
	    ->fieldLabel($form->getLabel('is_active'))
	    ->inputValue(1)
	    ->uncheckedValue(0)
	    ->allowBlank(false)
    )
    
    ->add(Ext::Hidden($form->generateName($form->getCSRFFieldname()))
    		->value($csrf_token)
    )
    
;



// function implementation ________________________________

$dlg->save->begin()?>
var me = this,
    form = Ext.getCmp('<?php echo $dlg->LocationForm->id ?>')
   ;

form.getForm().submit({
    clientValidation: true,
    submitEmptyText: false,
    url: '<?php echo ExtProxy::createUrl('save', $this) ?>',
    params: {},
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

// template methods _______________________________________

$dlg->beginMethod("initDialog()") ?>

this.window.setDisabled(true);


var me = this,
    form = Ext.getCmp('<?php echo $dlg->LocationForm->id ?>');
form.getForm().reset();

if (this.params.id) {
	form.load({
		url: '<?php echo ExtProxy::createUrl("location/location/getRecordData") ?>',
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
    me.window.setDisabled(false);
 
}

this.callParent(arguments);

<?php $dlg->endMethod();


$dlg->beginMethod("onRender()"); // Ha már tényleg megjelent a dialógus a DOM-ban, akkor lehet KeyMap-et tenni rá ?>
var me = this;

this.callParent(arguments);
<?php $dlg->endMethod();


$dlg->render();
