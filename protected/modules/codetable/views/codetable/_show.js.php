// <script type="text/javascript">

<?php

$dlg = new ExtDialog($this, array(
    "cacheable" => false,
    "title" => "{$model_label} szerkesztése",
    'credentials' => array('isSuperAdmin', 'editIndividualPermissions'),
));

$dlg->createMethod("onClickButtonSave(btn, pressed)", "this.save()");
$dlg->createMethod("save()");


$dlg->window->width(640)->autoHeight(true)->resizable(true);
$dlg->window->buttons(array(
        Ext::Button()->text('Mentés')->handler($dlg->onClickButtonSave),
        Ext::Button()->text('Bezár')->handler(new ExtFunction("this.window.close();"))->scope(new ExtCodeFragment("this")),
    ));

$dlg->add(Ext::CodetableForm('Form', $dlg, $form));

$dlg->save->begin(); ?>
    var form = Ext.getCmp('<?php echo $dlg->Form->id ?>');
    var me = this;

    form.getForm().submit({
        params : {model_name : this.params.model_name},
        success: function(form, action) {
					me.changed = false;
					me.parentWindow.fireEvent('refresh');
					me.window.close();
        },
        failure: theApp.handleFormSubmitFailure,
        waitTitle: MESSAGES.SAVE_WAIT_TITLE,
        waitMsg: MESSAGES.SAVE_WAIT_MESSAGE
    });

<?php $dlg->save->end();

$dlg->beginMethod("initDialog()") ?>
	var form = Ext.getCmp('<?php echo $dlg->Form->id ?>');

	if(this.params.id){
		form.load({
			url: '<?php echo ExtProxy::createUrl("codetable/codetable/getRecordData"); ?>',
			params: {
				model_name: this.params.model_name,
				id: this.params.id
			}
		});
	}
	else {
		form.getForm().reset();
		this.window.setTitle('Új <?php echo strtolower($model_label) ?> felvétele');
	}

	this.callParent();
	return true; <?php $dlg->endMethod();
