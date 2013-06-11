// <script type="text/javascript">
<?php

$mdl = new ExtModule($this, array(
    "cacheable" => false,
    "title" => 'Helyek'
));


// member variables______________________________________________

// functions, event handlers (declarations) ________________

// event handlers




// methods

$mdl->onDatachanged(Ext::fn("this.stores.get('GridStore').load();"));

$mdl->createMethod("openRecord(grid, record, action, row, col)");
$mdl->createMethod("addRecord(btn, pressed)", "theApp.showDialog('location/location/show', null, this);");

$mdl->createMethod('deleteLocation(grid, record)', "
	this.deleteLocationRecord(grid, record);
");

$mdl->createMethod("deleteLocationRecord(grid, record)");
// models and stores ______________________________________

$mdl->createModel("LocationData", $this->listFieldDefinitions());



$mdl->createStore("GridStore")
    ->model($mdl->model("LocationData"))
    ->autoLoad(false)
    ->remoteSort(true)
    ->remoteFilter(true)
    ->proxy(Ext::Proxy()
        ->url("getList", $this)
        ->reader(Ext::JsonReader())
    )
;

// view  __________________________________________________


$mdl->add(Ext::GridPanel("Grid")
    ->store($mdl->store("GridStore"))
    ->preventHeader(true)
    ->collapsible(true)
    ->iconCls('icon-grid')
    ->bbar(Ext::PagingToolbar()
        ->add(Ext::ToolbarSeparator())
        ->add(Ext::Button("ButtonNewRecord")
            ->iconCls('icon-add')
            ->text('Új hely')
            ->handler($mdl->addRecord)
    ))
    ->plugins(array(
        new ExtCodeFragment("Ext.create('Ext.ux.grid.plugin.HeaderFilters', { pluginId: 'gridFilters', enableTooltip : false })")
    ))
    ->autoExpandColumn('name')
    ->listeners(array(
        'itemdblclick'=> $mdl->openRecord,
        'scope' => new ExtCodeFragment('this'),
    ))
    ->rowaction(Ext::RowAction("edit")
        ->iconCls('icon-edit-record')
        ->qtip('Szerkesztés')
        ->callback($mdl->openRecord)
    )
    ->rowaction(Ext::RowAction("remove")
    		->iconCls('icon-remove')
    		->qtip('Törlés')
    		->callback($mdl->deleteLocation)
    )
)
;


// function implementation ________________________________

$mdl->openRecord->begin() //(grid, record, action, row, col) ?>
    theApp.showDialog('location/location/show', {
        id: record.data.id
    }, this);
<?php $mdl->openRecord->end();


$mdl->deleteLocationRecord->begin(); ?>
	var me = this;
	Ext.Msg.show({
		title: '<?php echo Yii::t('msg', "hely törlése") ?>',
		msg: '<?php echo Yii::t('msg', "Biztosan törli a helyet?") ?>',
		buttons: Ext.Msg.YESNO,
		fn: function(buttonId) {
			if (buttonId=='yes') {
				Ext.Ajax.request({
					url: '<?php  echo ExtProxy::createUrl("deleteLocation", $this) ?>',
					method: 'POST',
					params: {
						id: record.data.id
					},
					success: function(r) {
						var response = Ext.decode(r.responseText);
						if (response.success) {
							me.stores.get('GridStore').load();
						} else {
							var errors = '<br /><br />';
							Ext.each(response.errors, function(error){
								errors += error+'<br />';
							});
							Ext.Msg.alert('Hiba!', response.message +errors);
						}						
					},
					failure: theApp.handleFailure
				});
			}
		},
		icon: Ext.MessageBox.QUESTION
	});
<?php $mdl->deleteLocationRecord->end();

// template methods _______________________________________
$mdl->beginMethod("initModule()") ?>

	var grid = Ext.getCmp('<?php echo $mdl->Grid->id; ?>');

	grid.getPlugin('gridFilters').clearFilters();

	var store = this.stores.get('GridStore');
	store.load({
		params:{
            start: 0,
            limit: <?php echo $max_per_page; ?>
        },
		scope: this,
		callback: function() {
			this.fireEvent('moduleready');
		}
	});
	this.callParent();
	return false;
	
<?php
$mdl->endMethod();
    
$mdl->render();

?>