// <script type="text/javascript">
<?php

$mdl = new ExtModule($this, array(
	"cacheable" => true,
	"title" => Yii::t('msg','Felhasználói csoportok'),
	"credentials" => array(
		'isSuperAdmin', 
		'user_group_show', 
		'user_group_edit', 
		'user_group_delete',
		'user_toggle_active'
	),
));

$max_per_page = Yii::app()->params['extjs_pager_max_per_page'];


// member variables______________________________________________



// functions, event handlers (declarations) ________________

// event handlers
$mdl->onDataChanged(Ext::fn("this.stores.get('UserGroup').load();"));

$mdl->createMethod("onEditClicked(grid, record, action, row, col)", "this.editGroup(record.data);");
$mdl->createMethod("onDeleteClicked(grid, record, action, row, col)", "this.deleteGroup(record.data)");
$mdl->createMethod("onItemDblClick(grid, record, action, row, col)", "this.editGroup(record.data);");

// methods
$mdl->createMethod("editGroup(group)");
$mdl->createMethod("deleteGroup(group)");
$mdl->createMethod("addNewUserGroup(group)");



// models and stores ______________________________________

$mdl->createModel("UserGroup", $this->listFieldDefinitions());
$mdl->createStore("UserGroup")
	->model($mdl->model("UserGroup"))
	->autoLoad(false)
	->remoteSort(true)
	->pageSize($max_per_page)
	->proxy(Ext::Proxy()->url("getList", $this)
		->reader(Ext::JsonReader())
		->listeners(array(
			'exception' => new ExtCodeFragment('theApp.handleStoreException'),
		))
	)
;


// view  __________________________________________________


$mdl->add(Ext::GridPanel("Grid")
	->store($mdl->store("UserGroup"))
//	->stripeRows(true)
//	->border(false)
	->bbar(Ext::PagingToolbar("Toolbar")
		->store($mdl->store("UserGroup"))
		->add(Ext::ToolbarSeparator())
		->add(Ext::Button("ButtonNewUserGroup")
			->iconCls('icon-add')
			->text(Yii::t('msg','Új felhasználói csoport'))
	))
	// rowaction megadasa, ha a rowactions objectumnak nem kell explicit konfig:
	// az ExtClasses-ban a rowactions keepSelection erteke alapertelmezes szerint true, ezert nem kell itt megadni explicit
	->rowaction(Ext::RowAction("edit")
		->iconCls('icon-edit-record')
		->qtip(Yii::t('msg','Szerkesztés'))
		->callback($mdl->onEditClicked) // automatikus delegalt letrehozasa a modul scope-javal
	)
	->rowaction(Ext::RowAction("delete")
		->iconCls('icon-remove')
		->qtip(Yii::t('msg','Törlés'))
		->callback($mdl->onDeleteClicked)
	)
)
;

// event handers:
Ext::w("ButtonNewUserGroup")->handler($mdl->addNewUserGroup);
Ext::w("Grid")->onItemDblClick($mdl->onItemDblClick);



// function implementation ________________________________
				
$mdl->editGroup->begin()?>
	if (typeof this.credentials.user_group_show != 'undefined' && this.credentials.user_group_show) {
		theApp.showDialog('user/userGroup/show', {groupId: group.id}, this);
	} else {
		Ext.Msg.alert('<?php echo Yii::t('msg', "Jogosulatlan adat kérés") ?>', '<?php echo Yii::t('msg', "Önnek nincs joga a művelet végrehajtására!") ?>');
	} <?php $mdl->editGroup->end();


$mdl->addNewUserGroup->begin()?>
	theApp.showDialog('user/userGroup/show', null, this);<?php $mdl->addNewUserGroup->end();

$mdl->deleteGroup->begin()?>
	if (typeof this.credentials.user_group_delete != 'undefined' && this.credentials.user_group_delete) {
		var me = this;
		Ext.Msg.show({
			title: '<?php echo Yii::t('msg', "Törlés megerősítése") ?>',
			msg: '<?php echo Yii::t('msg', "Valóban törli?") ?>',
			buttons: Ext.Msg.YESNO,
			fn: function(buttonId) {
				if (buttonId == 'yes') {
					Ext.Ajax.request({
						url: '<?php  echo ExtProxy::createUrl("delete", $this) ?>',
						params: {
							record_id: group.id
						},
						success: function(response){
							var resp = Ext.decode(response.responseText);
							if (!resp.success) {
								theApp.handleSuccessFailure(resp);
							}
							else{
								me.stores.get('UserGroup').load();
							}
						},
						failure: theApp.handleFormFailure
					});
					return true;
				}
			},
			icon: Ext.window.MessageBox.QUESTION
		});
	}
	else {
		Ext.Msg.alert('<?php echo Yii::t('msg', "Jogosulatlan adat kérés") ?>', '<?php echo Yii::t('msg', "Önnek nincs joga a művelet végrehajtására!") ?>');
	} <?php $mdl->deleteGroup->end();


	
// template methods _______________________________________

$mdl->beginMethod("initModule()") ?>
	var me = this;
	this.stores.get('UserGroup').load({
		params:{
			start: 0,
			limit: <?php echo $max_per_page ?>
		},
		callback: function() {
			if (typeof me.credentials.user_group_edit != 'undefined' && me.credentials.user_group_edit) {
				Ext.getCmp('<?php echo Ext::w('ButtonNewUserGroup')->id ?>').setDisabled(false);		
			}
		}
	});
	return true;<?php $mdl->endMethod();

	
$mdl->render();
