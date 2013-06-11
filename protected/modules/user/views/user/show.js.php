// <script type="text/javascript">

<?php

// module definition ______________________________________

$dlg = new ExtDialog($this, array(
	"cacheable" => false,
	"title" => Yii::t('msg','Felhasználó adatai'),
));


// functions, event handlers (declarations) ________________

$dlg->createMethod("onClickButtonSave(btn, pressed)", "this.saveProfile()");
$dlg->createMethod("onLoadPermissionStore(store, records)", "this.populateUserPermissions");
$dlg->createMethod("onLoadGroupStore(store, records)", "this.populateUserGroups");

$dlg->createMethod("saveProfile()");
$dlg->createMethod("passwordValidator(password)");
$dlg->createMethod("populateUserPermissions()");
$dlg->createMethod("populateUserGroups()");


// models and stores ______________________________________

$dlg->createModel("Permission", $this->listAllPermissionsFieldDefinitions());
$dlg->createModel("Group", $this->listAssociatedGroupsFieldDefinitions());

$dlg->createStore("Permission")
	->model($dlg->model("Permission"))
	->autoLoad(false)
	->remoteSort(true)
	->pageSize(10000) // nem lehet megadni, hogy ne küldje a store a page paramétereket
	->proxy(Ext::Proxy()->url("user/permission/getList", $this)
		->reader(Ext::JsonReader())
	)
	->listeners(array(
		"load" => $dlg->onLoadPermissionStore,
		"scope" => new ExtCodeFragment("this")
	))
;
$dlg->createStore("Group")
	->model($dlg->model("Group"))
	->autoLoad(false)
	->remoteSort(true)
	->pageSize(10000) // nem lehet megadni, hogy ne küldje a store a page paramétereket
	->proxy(Ext::Proxy()->url("user/userGroup/getList", $this)
		->reader(Ext::JsonReader())
	)
	->listeners(array(
		"load" => $dlg->onLoadGroupStore,
		"scope" => new ExtCodeFragment("this")
	))
	
;


// view  __________________________________________________

$dlg->window->width(545)->height(350)->border(false)->autoHeight(true);
$dlg->window->buttons(array(
	Ext::Button()->text('Mentés')->handler($dlg->onClickButtonSave),
	Ext::Button()->text('Bezár')->handler(new ExtFunction("this.window.close()"))->scope(new ExtCodeFragment("this")),
));

// tab panel
$dlg->add(Ext::Form("Form")
	->border(false)
	->autoHeight(true)
	->labelWidth(120)
	->style("padding: 5px;")
	->add(Ext::TabPanel("Tabs")
		->height(280)
		->border(false)
		->frame(true)
		->deferredRender(false)
		->layoutOnTabChange(true)
		->defaults(array("layout"=>"form", "frame" => true, "hideMode" => "offsets", "border" => false))
		->add(Ext::Widget("UserTab")
			->title("Adatok")
			->layout('column')
			->add(Ext::FieldSet("UserForm")
				->columnWidth(.5)
				->labelWidth(100)
				->title(Yii::t('msg','Felhasználó adatok'))
				->height(244)
				->style("margin-right:5px;padding:5px;")
			)
			->add(Ext::FieldSet("ProfileForm")
				->columnWidth(.5)
				->title($form->getLabel('profile'))
				->labelWidth(80)
				->height(244)
				->style("padding:5px;")
			))
		->add(Ext::Widget("GroupsTab")
			->title($form->getLabel("groups_list"))
			->layout("fit")
			->style("padding: 5px;")
		)
		->add(Ext::Widget("PermissionsTab")
			->title($form->getLabel("permissions_list"))
			->layout("fit")
			->style("padding: 5px;")
		)
	)
);

// szemelyes adatok
$dlg->Form->Tabs->UserTab->UserForm
	->add(Ext::Hidden($form->generateName('id')))
	->add(Ext::Hidden($form->generateName($form->getCSRFFieldname()))->value($form->generateCsrfToken()))
	->add(Ext::TextField($form->generateName('username'))
		->fieldLabel($form->getLabel('username'))
		->value('')
	)
	->add(Ext::TextField($form->generateName('loginname'))
		->fieldLabel($form->getLabel('loginname'))
		->value('')
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
	->add(Ext::Checkbox($form->generateName('is_active'))
		->fieldLabel($form->getLabel('is_active'))
		->inputValue(1)
		->uncheckedValue(0)
	)
	->add(Ext::Checkbox($form->generateName('is_super_admin'))
		->fieldLabel($form->getLabel('is_super_admin'))
		->inputValue(1)
		->uncheckedValue(0)
	)
	->add(Ext::DisplayField($form->generateName('last_login'))
		->fieldLabel($form->getLabel('last_login'))
	)
	->add(Ext::DisplayField($form->generateName('created_at'))
		->fieldLabel($form->getLabel('created_at'))
	)
;


// felhasznaloi adatok
$dlg->Form->Tabs->UserTab->ProfileForm
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
$dlg->Form->Tabs->GroupsTab
	->add(Ext::ItemSelector("groups_list")
		->store($dlg->store("Group"))
		->displayField('name')
		->valueField('id')
		->hideLabel(true)
		->buttons(array('add','remove'))
		->fromTitle(Yii::t('msg','Csoportok'))
		->toTitle(Yii::t('msg','Hozzárendelve'))
	);
$dlg->Form->Tabs->PermissionsTab
	->add(Ext::ItemSelector("permissions_list")
		->store($dlg->store("Permission"))
		->multiSelect(false)
		->displayField('description')
		->valueField('id')
		->hideLabel(true)
		->buttons(array('add','remove'))
		->fromTitle(Yii::t('msg','Egyéni jogosultságok'))
		->toTitle(Yii::t('msg','Hozzárendelve'))
  );
;


// function implementation ________________________________

$dlg->onLoadPermissionStore->begin()?>
	if(this.params.userId) {
		Ext.Ajax.request({
			url: '<?php echo ExtProxy::createUrl("user/permission/getList") ?>',
			params: {
				user_id: this.params.userId,
				sort: '[{\"property\":\"description\",\"direction\":\"ASC\"}]'
			},
			success: function(response) {
				var resp = Ext.decode(response.responseText);
				var id_list = [];

				Ext.Array.each(resp.data, function(name, index) {
					id_list.push(name.id);
				});
				Ext.getCmp('<?php echo Ext::w("permissions_list")->id ?>').setValue(id_list);
		},
		failure: theApp.handleFailure
		});
	} 
<?php $dlg->onLoadPermissionStore->end();
		

$dlg->onLoadGroupStore->begin()?>
	if (typeof store.proxy.getReader().rawData.credentials != 'undefined') {
		credentials = store.proxy.getReader().rawData.credentials;
	}
	
	if(this.params.userId) {
		Ext.Ajax.request({
			url: '<?php echo ExtProxy::createUrl("user/userGroup/getList") ?>',
			params: {
				user_id: this.params.userId,
				sort: '[{\"property\":\"id\",\"direction\":\"ASC\"}]'
			},
			success: function(response) {
				var resp = Ext.decode(response.responseText);
				var id_list = [];
				Ext.Array.each(resp.data, function(name, index) {
					id_list.push(name.id);
				});
				Ext.getCmp('<?php echo Ext::w("groups_list")->id ?>').setValue(id_list);
			},
			failure: theApp.handleFailure
		});
	}
	<?php $dlg->onLoadGroupStore->end();

$dlg->passwordValidator->begin()?>
	if (/^(|<?php echo Yii::app()->params['password_validator_pattern'] ?>)$/.test(password)) {
		return true;
	}
	else {
		return '<?php echo Yii::t('msg', "A megadott jelszó nem megfelelő. Kérem használjon erős jelszavakat!") ?>"<br /><?php echo Yii::app()->params['password_validator_msg'] ?>';
	} <?php $dlg->passwordValidator->end();


$dlg->saveProfile->begin()?>
	var form = Ext.getCmp('<?php echo $dlg->Form->id ?>');
	var me = this;
	
	form.getForm().submit({
		clientValidation: true,
		url: '<?php echo ExtProxy::createUrl("save", $this) ?>',
		success: function(form, action) {
            me.changed = false;
            me.window.close();

            // szülő értesítése az adatok megváltozásáról
            me.parentWindow.fireEvent('userdatachanged', me.params.userId);
		},
		failure: theApp.handleFormSubmitFailure,
		waitTitle: MESSAGES.SAVE_WAIT_TITLE,
		waitMsg: MESSAGES.SAVE_WAIT_MESSAGE
	}); <?php $dlg->saveProfile->end();
			
			
// template methods _______________________________________

$dlg->beginMethod("initDialog()") ?>
	var me = this;
	
	Ext.getCmp('<?php echo $dlg->Form->Tabs->id ?>').setActiveTab(0);

	this.window.setDisabled(true);
		
	var form = Ext.getCmp('<?php echo $dlg->Form->id ?>');
	form.getForm().reset();

	if (this.params.userId) {
		form.load({
			url: '<?php echo ExtProxy::createUrl("user/defaultUser/fillProfileForm") ?>',
			params:{
				id: this.params.userId,
				'credentials[]': ['isSuperAdmin','editIndividualPermissions']
			},
			success: function(form, action) {
				me.window.setDisabled(false);
				Ext.getCmp('<?php echo Ext::w($form->generateName('is_super_admin'))->id ?>').setDisabled(!action.result.credentials.isSuperAdmin);

				if(!action.result.credentials.editIndividualPermissions){
					Ext.getCmp('<?php echo Ext::w("Tabs")->id ?>').getTabBar().items.get(2).hide();
				}
			},
			failure: theApp.handleFormFailure
		});
	}
	else {
		Ext.Ajax.request({
			url: '<?php echo ExtProxy::createUrl("getCredentials", $this) ?>',
			method: 'POST',
			params: {
				'credentials[]': ['isSuperAdmin','editIndividualPermissions']
			},
			success: function(r) {
				me.window.setDisabled(false);
				var response = Ext.decode(r.responseText);
				var isSuperAdmin = false;

				if (typeof response.credentials != 'undefined') {
					isSuperAdmin = response.credentials.isSuperAdmin;
					if(!response.credentials.editIndividualPermissions) {
						Ext.getCmp('<?php echo Ext::w("Tabs")->id ?>').getTabBar().items.get(2).hide();
					}
				}
				Ext.getCmp('<?php echo Ext::w($form->generateName('is_super_admin'))->id ?>').setDisabled(!isSuperAdmin);
			},
			failure: theApp.handleFailure
		});
	}

	this.stores.get('Group').load();
	this.stores.get('Permission').load();
	
	this.callParent();
	return true; <?php $dlg->endMethod();

$dlg->render();
