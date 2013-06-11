// <script type="text/javascript">
<?php

$mdl = new ExtModule($this, array(
	"cacheable" => true,
	"title" => Yii::t('msg','Jogosultságok'),
	"credentials" => array(
		'isSuperAdmin', 
		'user_permission_show', 
		'user_permission_reload',
	),
));

$max_per_page = Yii::app()->params['extjs_pager_max_per_page'];


// member variables______________________________________________



// functions, event handlers (declarations) ________________

// event handlers

$mdl->createMethod("onItemDblClick(grid, record, action, row, col)", "this.showPermission(record.data);");

$mdl->createMethod("showPermission(permission)");
$mdl->createMethod("reloadPermissions()");


// models and stores ______________________________________

$mdl->createModel("Permission", $this->listFieldDefinitions());
$mdl->createStore("Permission")
	->model($mdl->model("Permission"))
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
	->store($mdl->store("Permission"))
//	->stripeRows(true)
//	->border(false)
	->bbar(Ext::PagingToolbar("Toolbar")
		->store($mdl->store("Permission"))
		->add(Ext::ToolbarSeparator())
		->add(Ext::Button("ButtonReload")
			->iconCls('icon-shield-add')
			->text(Yii::t('msg','Jogosultságok újratöltése'))
			->disabled(true)
	))
	// rowaction megadasa, ha a rowactions objectumnak nem kell explicit konfig:
	// az ExtClasses-ban a rowactions keepSelection erteke alapertelmezes szerint true, ezert nem kell itt megadni explicit
	->rowaction(Ext::RowAction("open")
		->iconCls('icon-open')
		->qtip(Yii::t('msg','Adatlap'))
		->callback($mdl->onItemDblClick) // automatikus delegalt letrehozasa a modul scope-javal
	)
)
;

// event handers:
Ext::w("ButtonReload")->handler($mdl->reloadPermissions);
Ext::w("Grid")->onItemDblClick($mdl->onItemDblClick);




// function implementation ________________________________
				
$mdl->showPermission->begin()?>
	if (typeof this.credentials.user_permission_show != 'undefined' && this.credentials.user_permission_show) {
		theApp.showDialog('user/permission/show', {permissionId: permission.id}, this);
	} else {
		Ext.Msg.alert('<?php echo Yii::t('msg', "Jogosulatlan adat kérés") ?>', '<?php echo Yii::t('msg', "Önnek nincs joga a művelet végrehajtására!") ?>');
	} <?php $mdl->showPermission->end();


$mdl->reloadPermissions->begin()?>
	if (typeof this.credentials.user_permission_reload != 'undefined' && this.credentials.user_permission_reload) {
		var me = this;
		Ext.Msg.show({
			title: '<?php echo Yii::t('msg', "Jogosultságok újratöltése") ?>',
			msg: '<?php echo Yii::t('msg', "Biztosan újratölti az összes jogosultságot?") ?>',
			buttons: Ext.Msg.YESNO,
			fn: function(buttonId) {
				if (buttonId=='yes') {
					Ext.Ajax.request({					
						url: '<?php echo ExtProxy::createUrl('reload', $this) ?>',
						method: 'POST',
						success: function(r) {
							var response = Ext.JSON.decode(r.responseText);
							if (response.success==true) {
								me.stores.get('Permission').loadPage(1);
							} else {	
								Ext.Msg.alert('<?php echo Yii::t('msg', "Hiba") ?>', response.error.message);
							}
						},
						failure: theApp.handleFailure
					});
				}
			},
			icon: Ext.MessageBox.QUESTION
		});
	}
	else {
		Ext.Msg.alert('<?php echo Yii::t('msg', "Jogosulatlan adat kérés") ?>', '<?php echo Yii::t('msg', "Önnek nincs joga a művelet végrehajtására!") ?>');
	} <?php $mdl->reloadPermissions->end();

	
// template methods _______________________________________

$mdl->beginMethod("initModule()") ?>
	var me = this;
	this.stores.get('Permission').load({
		params:{
			start: 0,
			limit: <?php echo $max_per_page ?>
		},
		callback: function() {
			if (typeof me.credentials.user_permission_reload != 'undefined' && me.credentials.user_permission_reload) {
				Ext.getCmp('<?php echo Ext::w('ButtonReload')->id ?>').setDisabled(false);		
			}
		}
	});
	return true;<?php $mdl->endMethod();

	
$mdl->render();
