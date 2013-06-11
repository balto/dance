// <script type="text/javascript">
<?php

// module definition ______________________________________

$dlg = new ExtDialog($this, array(
	"cacheable" => false,
	"title" => Yii::t('msg','Felhasználói csoport adatai'),
	"credentials" => array('user_group_edit')
));

// functions, event handlers (declarations) ________________

$dlg->createMethod("onClickButtonSave(btn, pressed)", "this.save()");
$dlg->createMethod("onLoadAllPermStore(store, records)");

$dlg->createMethod("save()");


// models and stores ______________________________________

$dlg->createModel("AllPerm", $this->listAllPermissionsFieldDefinitions());
$dlg->createModel("AssociatedPerm", $this->listAssociatedPermissionsFieldDefinitions());

$dlg->createStore("AllPerm")
	->model($dlg->model("AllPerm"))
	->autoLoad(false)
	->remoteSort(true)
	->pageSize(10000) // nem lehet megadni, hogy ne küldje a store a page paramétereket
	->proxy(Ext::Proxy()->url("getAvailablePermissions", $this)
		->reader(Ext::JsonReader())
		->listeners(array('exception' => new ExtCodeFragment('theApp.handleStoreException')))
	)
	->listeners(array(
		"load" => $dlg->onLoadAllPermStore,
		"scope" => new ExtCodeFragment("this")
	))
;



// view  __________________________________________________

$dlg->window->width(545)->height(320)->border(false)->autoHeight(true);
$dlg->window->buttons(array(
	Ext::Button("ButtonSave")->text('Mentés')->handler($dlg->onClickButtonSave),
	Ext::Button()->text('Bezár')->handler(new ExtFunction("this.window.close()"))->scope(new ExtCodeFragment("this")),
));

// tab panel
$dlg->add(Ext::Form("Form")
	->border(false)
	->autoHeight(true)
	->labelWidth(70)
	//	->style("padding: 5px;")
	->add(Ext::TabPanel("Tabs")
		->activeTab(0)
		->height(260)
		->border(false)
		->frame(true)
		//->deferredRender(false)
		->defaults(array("layout"=>"form","frame"=>true,"hideMode"=>"offsets","border"=>false,"labelwidth"=>70))
		->add(Ext::Widget("DataTab")
			->title("Adatok")
			->style('padding:5px')
		)
		->add(Ext::Widget("RightsTab")
			->title("Jogosultságok")
			->style('padding:5px')
			->layout("fit")			
		)
	)
);

// adatok
$dlg->Form->Tabs->DataTab
	->add(Ext::Hidden($form->generateName('id')))
	->add(Ext::Hidden($form->generateName($form->getCSRFFieldname()))->value($form->generateCsrfToken()))
	->add(Ext::TextField($form->generateName('name'))
		->fieldLabel($form->getLabel('name'))
		->allowBlank(false)
		->value('')
		->anchor('100%')
	)
	->add(Ext::TextArea($form->generateName('description'))
		->fieldLabel($form->getLabel('description'))
		->value('')
		->anchor('100%')
		->height('100%')
  )
;


// jogosultsagok
$dlg->Form->Tabs->RightsTab
	->add(Ext::ItemSelector("permissions_list")
		->store($dlg->store("AllPerm"))
		->displayField('description')
		->valueField('id')
		->hideLabel(true)
		->buttons(array('add','remove'))
		->fromTitle('Választható jogosultságok')
		->toTitle('Hozzárendelve')
	)
	->onAfterLayout(new ExtFunction("Ext.getCmp('".Ext::w('permissions_list')->id."').setDisabled(!this.credentials.user_group_edit);"))
;


// function implementation ________________________________

$dlg->onLoadAllPermStore->begin()?>
	if(this.params.groupId) {
		Ext.Ajax.request({
			url: '<?php echo ExtProxy::createUrl("user/userGroup/getAssociatedPermissions") ?>',
			params: {
				id: this.params.groupId,
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
<?php $dlg->onLoadAllPermStore->end();
		

$dlg->save->begin()?>
	var form = Ext.getCmp('<?php echo $dlg->Form->id ?>');
	var me = this;
	
	if(form.getForm().isValid()) {
		form.getForm().submit({
			url: '<?php echo ExtProxy::createUrl("save", $this) ?>',
			success: function(form, action) {
				me.changed = false;
				me.window.close();
				me.parentWindow.fireEvent('datachanged', me.params.groupId);
			},
			failure: theApp.handleFormFailure,
			waitTitle: MESSAGES.SAVE_WAIT_TITLE,
			waitMsg: MESSAGES.SAVE_WAIT_MESSAGE
		});
	} <?php $dlg->save->end();

			
// template methods _______________________________________

$dlg->beginMethod("initDialog()") ?>
	var me = this;		
	
	var form = Ext.getCmp('<?php echo $dlg->Form->id ?>');
	form.getForm().reset();
	
	Ext.getCmp('<?php echo $dlg->Form->Tabs->id ?>').setActiveTab(0);
	Ext.getCmp('<?php echo Ext::w($form->generateName('id'))->id ?>').setValue(this.params.groupId);
		
	if (this.params.groupId) {
		this.window.setDisabled(true);
		form.load({
			url: '<?php echo ExtProxy::createUrl("fillForm", $this) ?>',
			params:{
				id: this.params.groupId
			},
			success: function(form, action) {
				me.window.setDisabled(false);				
			},
			failure: theApp.handleFormFailure
		});
	}
	
	this.stores.get('AllPerm').load({
		scope: this,
		callback: function() {
			if (typeof this.credentials.user_group_edit != 'undefined') {
				Ext.getCmp('<?php echo Ext::w('ButtonSave')->id ?>').setVisible(this.credentials.user_group_edit);
				Ext.getCmp('<?php echo Ext::w($form->generateName('name'))->id ?>').setReadOnly(!this.credentials.user_group_edit);
				Ext.getCmp('<?php echo Ext::w($form->generateName('description'))->id ?>').setReadOnly(!this.credentials.user_group_edit);
			}			
		}
	});	
	
	this.callParent();
	return true; <?php $dlg->endMethod();

$dlg->render();
