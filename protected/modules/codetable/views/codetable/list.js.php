// <script type="text/javascript">

<?php

$mdl = new ExtModule($this, array(
    'cacheable' => false,
    'title' => $model_label,
    'credentials' => array('isSuperAdmin', 'codetable_toggle_active'),
));

$mdl->createModel($model_name, $fields);

$mdl->createStore($model_name."Store")
    ->model($mdl->model($model_name))
    ->remoteSort(true)
    ->autoLoad(false)
    ->pageSize($max_per_page)
    ->proxy(Ext::Proxy()->url("getList", $this)
        ->extraParams(array('model_name' => $model_name, 'active_only' => 0))
        ->reader(Ext::JsonReader())
    );

// event handlers
$mdl->onRefresh(Ext::fn("this.stores.get('{$model_name}Store').load();"));

$mdl->createMethod("editRecord(grid, record, action, row, col)");
$mdl->createMethod("addRecord(btn, pressed)", "theApp.showDialog('codetable/codetable/show', this.params, this);");
$mdl->createMethod("removeRecord(grid, record, action, row, col)");
$mdl->createMethod("onItemClick(grid, record, item, index, e, options");
$mdl->createMethod("onItemDblClick(grid, record, item, index, e, options", "this.editRecord(grid,record)");

$mdl->add(Ext::GridPanel("Grid")
    ->store($mdl->store($model_name."Store"))
    ->preventHeader(true)
    ->collapsible(true)
    ->iconCls('icon-grid')
    ->bbar(Ext::PagingToolbar()
        ->layout('hbox')
        ->add(Ext::ToolbarSeparator())
        ->add(Ext::Button("ButtonNewRecord")
            ->iconCls('icon-add')
            ->text('Új ' . strtolower($model_label))
            ->handler($mdl->addRecord)
        )
    )
    ->rowaction(Ext::RowAction("edit")
        ->iconCls('icon-edit-record')
        ->qtip('Szerkesztés')
        ->callback($mdl->editRecord)
    )
    ->rowaction(Ext::RowAction("remove")
        ->iconCls('icon-remove')
        ->qtip('Törlés')
        ->callback($mdl->removeRecord)
    )
)
;

Ext::w("Grid")->onItemClick($mdl->onItemClick);
Ext::w("Grid")->onItemDblClick($mdl->onItemDblClick);

$mdl->beginMethod("initModule()") ?>
	
	var store = this.stores.get('<?php echo $model_name."Store"; ?>');
	
	// holiday_allocation_reason torzsadathoz
	if (this.params.special != undefined) {
		store.getProxy().extraParams.where = Ext.encode({special:this.params.special});
	}
	
	store.load({
		scope: this,
		callback: function() {
			this.fireEvent('moduleready');
		}
	});

	this.callParent();
	return false;
<?php $mdl->endMethod();

$mdl->editRecord->begin() //(grid, record, action, row, col) ?>
	// TODO: attól függően kellene másik view-t hívni, hogy van-e show{$model_name} nevű osztály (view) definiálva
	var params = {};
	Ext.apply(params, this.params);
	params.id = record.data.id;
	
	theApp.showDialog('codetable/codetable/show', params, this);
<?php $mdl->editRecord->end();

$mdl->removeRecord->begin() //(grid, record, action, row, col) ?>
var me = this,
    deleted_data = record.data.name;

Ext.Msg.show({
    title: 'Törlés',
    msg: "Biztosan törli?",
    buttons: Ext.Msg.YESNO,
    fn: function(buttonId) {
        if (buttonId=='yes') {
            Ext.Ajax.request({
                url: '<?php  echo ExtProxy::createUrl("delete", $this) ?>',
                method: 'POST',
                params: {
                    model_name: '<?php echo $model_name; ?>',
                    id: record.data.id
                },
                success: function(r) {
                    var response = Ext.decode(r.responseText);
                    if (response.success) {
                        Ext.example.msg(deleted_data, response.message);
                        me.stores.get('<?php echo $model_name; ?>Store').load();
                    } else {
                        var errors = '<br /><br />';
                        Ext.each(response.errors, function(error){
                            errors += error+'<br />';
                        });
                        Ext.Msg.alert('Hiba', response.message +errors);
                    }
                },
                failure: theApp.handleFailure
            });
        }
    },
    icon: Ext.MessageBox.QUESTION
});
<?php $mdl->removeRecord->end();


$mdl->onItemClick->begin() //(grid, record, item, index, e, options) ?>
var me = this;
if (
    (typeof this.credentials.codetable_toggle_active != 'undefined' && this.credentials.codetable_toggle_active)
    ||
    (typeof this.credentials.isSuperAdmin != 'undefined' && this.credentials.isSuperAdmin)
   ){
        if (e.target.className.indexOf('active-state')>=0) {
            Ext.Ajax.request({
                url: '<?php echo ExtProxy::createUrl("toggleCodeRecordActive", $this) ?>',
                params: {
                    model_name: me.params.model_name,
                    id: record.get('id')
                },
                success: function(response){
                    var resp = Ext.decode(response.responseText);
                    if (!resp.success) theApp.handleSuccessFailure(resp);
                    else {
                        Ext.example.msg(me.params.model_label, resp.message);
                        me.stores.get('<?php echo $model_name; ?>Store').load();
                    }
                },
                failure: theApp.handleFailure
            });
        }
    }
<?php $mdl->onItemClick->end();


$mdl->render();