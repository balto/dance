// <script type="text/javascript">
<?php

// module definition ______________________________________

$dlg = new ExtDialog($this, array(
    "cacheable" => true,
    "title" => 'Jogosult kampány típusok',
    'layout' => 'anchor',
));


// functions, event handlers (declarations) ________________

$dlg->createMethod("done()");

// models and stores ______________________________________

$dlg->createModel("CampaignTypePermission", $this->campaignTypelistFieldDefinitions(true, true));

$dlg->createStore("GridPStore")
	->model($dlg->model("CampaignTypePermission"))
	->autoLoad(false)
	->remoteSort(true)
	->remoteFilter(true)
	->proxy(Ext::Proxy()
		->url("campaign/campaignType/getCampaignTypeList", $this)
		->reader(Ext::JsonReader())
		->extraParams(array('isCtPermissionShow' => 1))
	)
;

// view  __________________________________________________

$dlg->window->width(400)->height(300);
$dlg->window->buttons(array(
    Ext::Button()->text('Ok')->handler($dlg->done),
    Ext::Button()->text('Bezár')->handler(new ExtFunction("this.window.close()"))->scope(new ExtCodeFragment("this")),
));

$dlg->add(Ext::Form('FormPerm')
		->border(false)
		->defaults(array("labelWidth" => 170, "anchor" => "100%"))
		->bodyPadding(5)
);

$dlg->add(Ext::GridPanel("GridPerm")
		->store($dlg->store("GridPStore"))
		->preventHeader(true)
		->collapsible(true)
		->selModel(new ExtCodeFragment("Ext.create('Ext.selection.CheckboxModel', { checkOnly : true })"))
		->height(200)
		->title('Kampány típus(ok)')
		->iconCls('icon-grid')
		->plugins(array(
				new ExtCodeFragment("Ext.create('Ext.ux.grid.plugin.HeaderFilters', { pluginId: 'gridFilters', enableTooltip : false })"),
				new ExtCodeFragment("Ext.create('Ext.grid.plugin.CellEditing', { clicksToEdit: 2})"),
		))
		->autoExpandColumn('name')
)
;

$dlg->add(Ext::Form('HelperPermForm')
		->border(false)
		->defaults(array("labelWidth" => 170, "anchor" => "100%"))
		->bodyPadding(5)
		->add(
			Ext::Hidden('helperPermInput')
			->value('')
		)
);


// function implementation ________________________________

$dlg->done->begin()?>
var me = this,
    grid = Ext.getCmp('<?php echo $dlg->GridPerm->id ?>')
   ;

	var helper = Ext.getCmp('<?php echo $dlg->HelperPermForm->helperPermInput->id; ?>');
	var parentGrid = Ext.getCmp(helper.getValue());
    var selModel = grid.getSelectionModel();
	var selected = selModel.getSelection();

	var ctForm = Ext.getCmp('<?php echo $dlg->FormPerm->id; ?>');
	
	if(ctForm.getForm().isValid()){

	    Ext.each(selected, function(model){
	    	parentGrid.getStore().add(model);
	   	});
	
	   	me.window.close();
	}

<?php $dlg->done->end();

// template methods _______________________________________

$dlg->beginMethod("initDialog()") ?>
var grid = Ext.getCmp('<?php echo $dlg->GridPerm->id ?>');
    
var me = this;

var helper = Ext.getCmp('<?php echo $dlg->HelperPermForm->helperPermInput->id ?>');
helper.setValue(this.params.gridId);

//console.log(helper.getValue());
//console.log(this.params.id);

if (this.params.id) {
	

} else {
    
}

grid.getStore().load();

this.callParent(arguments);

<?php $dlg->endMethod();


$dlg->beginMethod("onRender()"); // Ha már tényleg megjelent a dialógus a DOM-ban, akkor lehet KeyMap-et tenni rá ?>
var me = this;

this.callParent(arguments);
<?php $dlg->endMethod();


$dlg->render();
