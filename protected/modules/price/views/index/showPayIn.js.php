// <script type="text/javascript">
<?php

// module definition ______________________________________

$dlg = new ExtDialog($this, array(
    "cacheable" => true,
    "title" => 'Befizetés',
    'layout' => 'anchor',
));


// functions, event handlers (declarations) ________________

$csrf_token = $form->generateCsrfToken();

$dlg->createMethod("save()");



// view  __________________________________________________

$dlg->window->width(300)->height(120);
$dlg->window->buttons(array(
    Ext::Button()->text('Befizet')->handler($dlg->save),
    Ext::Button()->text('Bezár')->handler(new ExtFunction("this.window.close()"))->scope(new ExtCodeFragment("this")),
));


$dlg->add(Ext::Form('PayInForm')
	->border(false)
	->defaults(array("labelWidth" => 70, "anchor" => "100%"))
	->bodyPadding(5)
	);

$dlg->PayInForm
	->add(Ext::Container('MemberName')
		->cls('pay_in_member_name')
    )

	->add(Ext::Hidden($form->generateName('id'))
    		->value('')
    ) 

    ->add(Ext::TextField($form->generateName('price'))
        ->fieldLabel($form->getLabel('price'))
        ->value('')
    	->allowBlank(false)
    )
    
    ->add(Ext::Hidden($form->generateName($form->getCSRFFieldname()))
    		->value($csrf_token)
    ) 
;

// function implementation ________________________________

$dlg->save->begin()?>
var me = this,
    form = Ext.getCmp('<?php echo $dlg->PayInForm->id ?>')
   ;

form.getForm().submit({
    clientValidation: true,
    submitEmptyText: false,
    url: '<?php echo ExtProxy::createUrl('savePayIn', $this) ?>',
    params: {},
    success: function(form, action) {
        me.changed = false;
        me.window.close();

		var error = action.result.error;

		if(error.length){
			Ext.Msg.alert('Hiba!',action.result.message+'<br />'+ action.result.error.join("\n"));
		}
		else{
			// ertesites az adatok megvaltozasarol
        me.parentWindow.fireEvent('paydatachanged', me.params.id);
		}
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
    form = Ext.getCmp('<?php echo $dlg->PayInForm->id ?>');
    
var nameContainer = Ext.getCmp('<?php echo $dlg->PayInForm->MemberName->id ?>');
    nameContainer.update(this.params.name);
form.getForm().reset();


if (this.params.id) {
	form.load({
		url: '<?php echo ExtProxy::createUrl("price/index/getPayInRecordData") ?>',
		params:{
			id: this.params.id,
			name: this.params.name,
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