// <script type="text/javascript">
<?php

$mdl = new ExtModule($this, array(
	"cacheable" => true,
	"title" => Yii::t('msg','Felhasználók'),
	"credentials" => array('isSuperAdmin', 'user_toggle_active'),
));


// member variables______________________________________________



// functions, event handlers (declarations) ________________

// event handlers
// az onItemDoubleClick() annyira enyszeru, hogy itt is kifejthetjuk a torzset:
$mdl->createMethod("onItemDoubleClick(grid, record, item, rowIndex, e, options)", "this.showUser(record.data);");
$mdl->createMethod("onItemClick(view, record, item, index, e, options");
$mdl->createMethod("onItemGeneratePassword(grid, record, action, row, col)", "this.generatePassword(record.data)");
$mdl->createMethod("onItemDelete(grid, record, item, rowIndex, e, options)", "this.deleteUser(record.data)");

// methods
$mdl->createMethod("addNewUser()");
$mdl->createMethod("showUser(user)");
$mdl->createMethod("deleteUser(user)");
$mdl->createMethod("generatePassword(user)");

// valtozas eseten ujratoltjuk a store-t
$mdl->onUserDataChanged(Ext::fn("this.stores.get('User').load()"));


// models and stores ______________________________________

$mdl->createModel("User", $this->listFieldDefinitions());
$mdl->createStore("User")
	->model($mdl->model("User"))
	->autoLoad(false)
	->remoteSort(true)
	->groupField('groups')
	->pageSize($max_per_page)
	->proxy(Ext::Proxy()->url("getList", $this)
		->reader(Ext::JsonReader())
	)
;


// view  __________________________________________________


$mdl->add(Ext::GridPanel("UserGrid")
	->store($mdl->store("User"))
	->preventHeader(true)
	->collapsible(true)
	->iconCls("icon-grid")
	->features(new ExtCodeFragment("
			new Ext.grid.feature.Grouping ({
			groupHeaderTpl: '{name}'
			})
		"))
	->bbar(Ext::PagingToolbar("Toolbar")
		->store($mdl->store("User"))
		->add(Ext::ToolbarSeparator())
		->add(Ext::Button("ButtonNewUser")
			->iconCls('icon-add')
			->text(Yii::t('msg','Új felhasználó'))
		))
	->autoExpandColumn('username')
	// rowaction megadasa, ha a rowactions objectumnak nem kell explicit konfig:
	// az ExtClasses-ban a rowactions keepSelection erteke alapertelmezes szerint true, ezert nem kell itt megadni explicit
	->rowaction(Ext::RowAction("open")
		->iconCls('icon-open')
		->qtip(Yii::t('msg','Adatlap'))
		->callback($mdl->onItemDoubleClick) // automatikus delegalt letrehozasa a modul scope-javal
	)
	->rowaction(Ext::RowAction("generate_password")
		->iconCls('icon-key')
		->qtip(Yii::t('msg','Jelszó generálás'))
		->callback($mdl->onItemGeneratePassword)
	)
	->rowaction(Ext::RowAction("remove")
		->iconCls('icon-remove')
		->qtip(Yii::t('msg','Felhasználó törlése'))
		->callback($mdl->onItemDelete)
	)
)
;

// event handers:
Ext::w("ButtonNewUser")->handler($mdl->addNewUser);
Ext::w("UserGrid")->onItemDblClick($mdl->onItemDoubleClick);
Ext::w("UserGrid")->onItemClick($mdl->onItemClick);


// function implementation ________________________________
				
$mdl->onItemClick->begin()?>
	var me = this;
	
	if ((typeof this.credentials.user_toggle_active != 'undefined' && this.credentials.user_toggle_active)
			|| (typeof this.credentials.isSuperAdmin != 'undefined' && this.credentials.isSuperAdmin)
	){
		if (e.target.className.indexOf('active-state')>=0) {
			var userId = record.get('id');

			Ext.Ajax.request({
				url: '<?php echo ExtProxy::createUrl("toggleUserIsActive", $this) ?>',
				params: {
				user_id: userId
				},
				success: function(response) {
				var resp = Ext.decode(response.responseText);
					if (!resp.success) ".$app_ns.".handleSuccessFailure(resp);
					else {
						Ext.Msg.alert('<?php echo $mdl->title ?>', resp.message);
						me.stores.get("User").load();
					}
				},
				failure: theApp.handleFailure
			});
		}
	}
		
	if (typeof this.credentials.isSuperAdmin != 'undefined' && this.credentials.isSuperAdmin) {
		if (e.target.className.indexOf('superadmin-state')>=0) {
			var userId = record.get('id');

			Ext.Ajax.request({
				url: '<?php echo  ExtProxy::createUrl("toggleUserIsSuperAdmin", $this) ?>',
				params: {
					user_id: userId
				},
				success: function(response) {
					var resp = Ext.decode(response.responseText);
					if (!resp.success) theApp.handleSuccessFailure(resp);
					else {
						Ext.Msg.alert('<?php echo $mdl->title ?>', resp.message);
						me.stores.load();
					}
				},
				failure: theApp.handleFailure
			});
		}
	}<?php $mdl->onItemClick->end();

$mdl->showUser->begin()?>
	theApp.showDialog('user/user/show', {userId: user.id}, this);<?php $mdl->showUser->end();

$mdl->addNewUser->begin()?>
	theApp.showDialog('user/user/show', null, this);<?php $mdl->addNewUser->end();

$mdl->deleteUser->begin()?>
	var me = this;
	Ext.Msg.show({
		title: '<?php echo Yii::t('msg', "Felhasználó törlése") ?>',
		msg: '<?php echo Yii::t('msg', "Biztosan törli <b>{0}</b> felhasználót?") ?>'.format(user.username),
		buttons: Ext.Msg.YESNO,
		fn: function(buttonId) {
			if (buttonId=='yes') {
				Ext.Ajax.request({
					url: '<?php  echo ExtProxy::createUrl("delete", $this) ?>',
					method: 'POST',
					params: {
						user_id: user.id
					},
					success: function(r) {
						var response = Ext.decode(r.responseText);
						if (response.success) {
							me.stores.get('User').load();
						} else {
							var errors = '<br /><br />';
							Ext.each(response.errors, function(error){
								errors += error+'<br />';
							});
							Ext.Msg.alert('".$panel_title."', response.message +errors);
						}						
					},
					failure: theApp.handleFailure
				});
			}
		},
		icon: Ext.MessageBox.QUESTION
	});<?php $mdl->deleteUser->end();

$mdl->generatePassword->begin()?>
	Ext.Msg.show({
		title: '<?php echo Yii::t('msg', "Jelszó generálás") ?>',
		msg: '<?php echo Yii::t('msg', "Biztosan új kezdeti jelszót kíván generálni <b>{0}</b> felhasználónak?") ?>'.format(user.username),
		buttons: Ext.Msg.YESNO,
		fn: function(buttonId) {
			if (buttonId=='yes') {
				Ext.Ajax.request({
					url: '<?php echo ExtProxy::createUrl("generatePassword", $this) ?>',
					method: 'POST',
					params: {
						userId: user.id					
					},
					success: function(r) {
						var response = Ext.decode(r.responseText);
						if (response.success==true) {
							Ext.Msg.alert('<?php echo $mdl->title ?>', response.message);
							this.stores.get("User").reload();
						} else {
							Ext.Msg.alert('<?php echo $mdl->title ?>', response.error.message);
						}
					},
					failure: theApp.handleFailure
				})
			}
		},
		icon: Ext.MessageBox.QUESTION
	});<?php $mdl->generatePassword->end();

	
// template methods _______________________________________

$mdl->beginMethod("initModule()") ?>
	this.stores.get('User').load({
		params:{
			start: 0,
			limit: <?php echo $max_per_page ?>
		}
	});
	return true;<?php 
$mdl->endMethod();

$mdl->render();
