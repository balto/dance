// <script type="text/javascript">

<?php

include "_show.js.php";

// special mezo hiddenbe:
$dlg->Form
	->remove($form->generateName("special"))
	->add(Ext::Hidden($form->generateName("special")))
;	

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
		// a 'special' hidden mezo beallitasa
		form.getForm().findField('<?php echo $form->generateName("special") ?>').setValue(this.params.special);
		this.window.setTitle('Új <?php echo strtolower($model_label) ?> felvétele');
	}

	this.callParent();
	return true; <?php $dlg->endMethod();


$dlg->render();

