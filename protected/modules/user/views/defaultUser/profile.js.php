// <script type="text/javascript">

<?php

// module definition ______________________________________

$dlg = new ExtDialog($this, array(
	"cacheable" => true,
	"title" => Yii::t('msg','Felhasználó adatok')
));


// functions, event handlers (declarations) ________________

$dlg->createMethod("onClickButtonSave(btn, pressed)", "this.saveProfile()");

$dlg->createMethod("saveProfile()");
$dlg->createMethod("passwordValidator(password)");


// models and stores ______________________________________

$dlg->createModel("Permission", $this->listAllPermissionsFieldDefinitions());
$dlg->createModel("Group", $this->listAssociatedGroupsFieldDefinitions());

$dlg->createStore("Permission")
	->model($dlg->model("Permission"))
	->remoteSort(true)
	->pageSize(7)
	->proxy(Ext::Proxy()->url("getAllPermissions", $this)
		->reader(Ext::JsonReader())
	)
;
$dlg->createStore("Group")
	->model($dlg->model("Group"))
	->remoteSort(true)
	->pageSize(7)
	->proxy(Ext::Proxy()->url("getAssociatedGroups", $this)
		->reader(Ext::JsonReader())
	)
;


// view  __________________________________________________

$dlg->window->width(545)->height(320)->border(false)->autoHeight(true)->labelWidth(120);
$dlg->window->buttons(array(
	Ext::Button()->text(Yii::t('msg','Mentés'))->handler($dlg->onClickButtonSave),
	Ext::Button()->text(Yii::t('msg','Bezár'))->handler(new ExtFunction("this.window.close()"))->scope(new ExtCodeFragment("this")),
));

// tab panel
$dlg->add(Ext::Form("Form")
	->border(false)
	->autoHeight(true)
	->labelWidth(120)
	->add(Ext::TabPanel("Tabs")
		->height(250)
		->border(false)
		->frame(true)
		->layoutOnTabChange(true)
		->defaults(array("layout"=>"form", "frame" => true, "hideMode" => "offsets"))
		->add(Ext::Widget("User")
			->title("Adatok")
			->layout('column')
			->add(Ext::FieldSet("UserForm")
				->columnWidth(.5)
				->labelWidth(100)
				->title(Yii::t('msg','Felhasználó adatok'))
				->minHeight(220)
				->style("margin-right:5px;padding:5px;")
			)
			->add(Ext::FieldSet("ProfileForm")
				->columnWidth(.5)
				->title(Yii::t('msg','Személyes adatok'))
				->labelWidth(80)
				->minHeight(220)
				->style("padding:5px;")
			))
		->add(Ext::Widget("GroupsAndPermissions")
			->title(Yii::t('msg','Csoportok és jogosultságok'))
			->layout("column")
		)
	)
);

// szemelyes adatok
$dlg->Form->Tabs->User->UserForm
	->add(Ext::Hidden($form->generateName('id')))
	->add(Ext::Hidden($form->generateName($form->getCSRFFieldname()))->value($form->generateCsrfToken()))
	->add(Ext::DisplayField($form->generateName('username'))
		->fieldLabel($form->getLabel('username'))
		->value('')
		->style('padding-top:3px;')
	)
	->add(Ext::TextField($form->generateName('loginname'))
		->fieldLabel($form->getLabel('loginname'))
		->value('')
  )
	->add(Ext::TextField($form->generateName('password_old'))
		->inputType('password')
		->fieldLabel($form->getLabel('password_old'))
	)
	->add(Ext::TextField($form->generateName('password'))
		->inputType('password')
		->fieldLabel($form->getLabel('password'))
		->validator($dlg->passwordValidator)
	)
	->add(Ext::TextField($form->generateName('password_again'))
		->inputType('password')
		->fieldLabel($form->getLabel('password_again'))
		->validator($dlg->passwordValidator)
	)
	->add(Ext::DisplayField($form->generateName('last_login'))
		->fieldLabel($form->getLabel('last_login'))
		->style('padding-top:3px;')
		->disabled(true)
	)
;


// felhasznaloi adatok
$dlg->Form->Tabs->User->ProfileForm
	->add(Ext::Hidden($profile_form->generateName($profile_form->getCSRFFieldname()))
		->value($profile_form->generateCsrfToken())
	)
	->add(Ext::TextField($profile_form->generateName('tel'))
		->fieldLabel($profile_form->getLabel('tel'))
	)
	->add(Ext::TextField($profile_form->generateName('mobil'))
		->fieldLabel($profile_form->getLabel('mobil'))
	)
	->add(Ext::TextField($profile_form->generateName('email'))
		->fieldLabel($profile_form->getLabel('email'))
  )
;

// csoportok es jogosultsagok
$dlg->Form->Tabs->GroupsAndPermissions
	->add(Ext::GridPanel()
		->columnWidth(.5)
		->store($dlg->store("Permission"))
		->multiSelect(false)
		->hideLabel(true)
		->border(true)
		->height(220)
		->autoScroll(true)
		->style("margin:5px;border:1px solid #aaa; overflow-y: auto; height: 198px")
		->listeners(array(
			"render" => new ExtFunction("this.store.load()")
		))
		->bbar(Ext::PagingToolbar())
  )
	->add(Ext::GridPanel()
		->columnWidth(.5)
		->store($dlg->store("Group"))
		->multiSelect(false)
		->hideLabel(true)
		->border(true)
		->height(220)
		->autoScroll(true)
		->style("margin:5px;border:1px solid #aaa; overflow-y: auto; height: 200px")
		->listeners(array(
			"render" => new ExtFunction("this.store.load()")
		))
		->bbar(Ext::PagingToolbar())
  )
;


// function implementation ________________________________

$dlg->passwordValidator->begin()?>
	if (/^(|<?php echo Yii::app()->params['password_validator_pattern'] ?>)$/.test(password)) {
		return true;
	}
	else {
		return '<?php echo Yii::t('msg', "A megadott jelszó nem megfelelő. Kérem használjon erős jelszavakat!") ?>"<br /><?php echo Yii::app()->params['password_validator_msg'] ?>';
	} <?php $dlg->passwordValidator->end();


$dlg->saveProfile->begin()?>
	var me = this;
	var form = Ext.getCmp('<?php echo $dlg->Form->id ?>');
	
	form.getForm().submit({
		url: theApp.getSystemBaseUrl()+'/user/defaultUser/saveProfile',
		success: function(form, action) {
			if (action.result.success==true) {
				me.window.close();
			} else {
				var errors = '<br /><br />';
				Ext.each(action.result.errors, function(error) {
					if (error.field && error.message) {
						errors += error.field+': '+error.message+'<br />';
					} else {
						errors += error+'<br />';
					}
				});
				Ext.Msg.alert('<?php echo Yii::t('msg', "Hiba az űrlapon") ?>', action.result.message+errors);
			}
		},
		failure: function(form, action) {
			switch (action.failureType) {
				case Ext.form.Action.CLIENT_INVALID:
					Ext.Msg.alert('<?php echo Yii::t('msg', "Hiba") ?>', '<?php echo Yii::t('msg', "Az űrlap kitöltése hibás, kérem ellenőrizze!") ?>');
					break;
				case Ext.form.Action.CONNECT_FAILURE:
					if (typeof action.response != 'undefined') {
						theApp.handleFailure(action.response, null);
					}	else {
						Ext.Msg.alert('<?php echo Yii::t('msg', "Belépés") ?>','<?php echo Yii::t('msg', "Hiba a kapcsolatban, kérjük próbálja meg ismét.")?>');
					}
					break;
				case Ext.form.Action.SERVER_INVALID:
					theApp.handleFormFailure(form, action);
			}	
		},
		waitTitle: '<?php echo Yii::t('msg', "Mentés") ?>',
		waitMsg: '<?php echo Yii::t('msg', "Személyes adatok rögzítése...") ?>'
	}); <?php $dlg->saveProfile->end();
			
			
// template methods _______________________________________

$dlg->beginMethod("initDialog()") ?>
	Ext.getCmp('<?php echo Ext::w($form->generateName('id'))->id ?>').setValue(this.params.userId);
	Ext.getCmp('<?php echo $dlg->Form->Tabs->id ?>').setActiveTab(0);
	
	var form = Ext.getCmp('<?php echo $dlg->Form->id ?>');
	
	form.getForm().reset();
	
	if (this.params.userId) {
		this.window.setDisabled(true);
		form.load({
			url: '<?php echo ExtProxy::createUrl('fillProfileForm', $this) ?>',
			params:{ id: this.params.userId },
			success: function() { this.window.setDisabled(false); },
			failure: theApp.handleFormFailure,
			scope: this
		});
	}
	
	<?php
$dlg->endMethod();

$dlg->render();
