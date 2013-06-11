// <script type="text/javascript">
<?php

// module definition ______________________________________

$dlg = new ExtDialog($this, array(
	"cacheable" => false,
	"title" => Yii::t('msg','Jogosultság adatai'),
));

// functions, event handlers (declarations) ________________


// models and stores ______________________________________


// view  __________________________________________________

$dlg->window->width(420)->height(320)->border(false)->autoHeight(true);
$dlg->window->buttons(array(
	Ext::Button()->text('Bezár')->handler(new ExtFunction("this.window.close()"))->scope(new ExtCodeFragment("this")),
));

$dlg->add(Ext::Form("Form")
	->border(false)
	->autoHeight(true)
	->labelWidth(120)
	->anchor('100% 100%')
	->add(Ext::TabPanel("Tabs")
		->activeTab(0)
		->height(250)
		->border(false)
		->frame(true)
		->defaults(array("layout"=>"form","frame"=>true,"hideMode"=>"offsets"))
		->add(Ext::Widget("DataTab")
			->title("Adatok")
			->layout(array('type' => 'column'))
			->style('padding:5px')
			->defaults(array(
				'style' => 'margin-bottom:5px;',
				'width' => 380,
			))
		)
	)
);


// adatok
$dlg->Form->Tabs->DataTab
	->add(Ext::Hidden($form->generateName('id')))
	->add(Ext::Hidden($form->generateName($form->getCSRFFieldname()))->value($form->generateCsrfToken()))
	->add(Ext::DisplayField($form->generateName('title'))
		->fieldLabel($form->getLabel('title'))
	)
	->add(Ext::DisplayField($form->generateName('name'))
		->fieldLabel($form->getLabel('name'))
	)
	->add(Ext::TextArea($form->generateName('description'))
		->fieldLabel($form->getLabel('description'))
		->height(100)
		->readOnly(true)
  )
;



// function implementation ________________________________

			
// template methods _______________________________________

$dlg->beginMethod("initDialog()") ?>
	var me = this;		
	
	var form = Ext.getCmp('<?php echo $dlg->Form->id ?>');
	form.getForm().reset();
	
	Ext.getCmp('<?php echo Ext::w($form->generateName('id'))->id ?>').setValue(this.params.permissionId);
		
	form.load({
		url: '<?php echo ExtProxy::createUrl("fillForm", $this) ?>',
		params:{
			id: this.params.permissionId
		},
		success: function(form, action) {
			me.window.setDisabled(false);				
		},
		failure: theApp.handleFormFailure
	});
		
	this.callParent();
	return true; <?php $dlg->endMethod();

$dlg->render();
