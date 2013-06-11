// <script type="text/javascript">
<?php

$mdl = new ExtModule($this, array(
    "cacheable" => false,
    "title" => 'Kampány típusok'
));


// member variables______________________________________________

// functions, event handlers (declarations) ________________

// event handlers




// methods

$mdl->onDatachanged(Ext::fn("this.stores.get('GridStore').load();"));

$mdl->createMethod("openRecord(grid, record, action, row, col)");
$mdl->createMethod("addRecord(btn, pressed)", "theApp.showDialog('campaign/campaignType/show', null, this);");

$mdl->createMethod('deleteCampaignType(grid, record)', "
	this.deleteCampaignTypeRecord(grid, record);
");

$mdl->createMethod("deleteCampaignTypeRecord(grid, record)");

// models and stores ______________________________________

$mdl->createModel("CampaignTypeData", $this->listFieldDefinitions());



$mdl->createStore("GridStore")
    ->model($mdl->model("CampaignTypeData"))
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
            ->text('Új kampány típus')
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
    		->callback($mdl->deleteCampaignType)
    )
)
;


// function implementation ________________________________

$mdl->openRecord->begin() //(grid, record, action, row, col) ?>
    theApp.showDialog('campaign/campaignType/show', {
        id: record.data.id
    }, this);
<?php $mdl->openRecord->end();

$mdl->deleteCampaignTypeRecord->begin(); ?>
	var me = this;
	Ext.Msg.show({
		title: '<?php echo Yii::t('msg', "Kampány típus törlése") ?>',
		msg: '<?php echo Yii::t('msg', "Biztosan törli a kampány típust?") ?>',
		buttons: Ext.Msg.YESNO,
		fn: function(buttonId) {
			if (buttonId=='yes') {
				Ext.Ajax.request({
					url: '<?php  echo ExtProxy::createUrl("deleteCampaignType", $this) ?>',
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
<?php $mdl->deleteCampaignTypeRecord->end();

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