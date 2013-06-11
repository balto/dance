// <script type="text/javascript">
<?php

// module definition ______________________________________

$dlg = new ExtDialog($this, array(
    "cacheable" => true,
    "title" => 'Kampány típusok',
    'layout' => 'anchor',
));


// functions, event handlers (declarations) ________________

$dlg->createMethod("done()");

// models and stores ______________________________________

$dlg->createModel("CampaignType", $this->listFieldDefinitions(true, true));

$dlg->createStore("GridStore")
	->model($dlg->model("CampaignType"))
	->autoLoad(false)
	->remoteSort(true)
	->remoteFilter(true)
	->proxy(Ext::Proxy()
		->url("getCampaignTypeList", $this)
		->reader(Ext::JsonReader())
	)
;

// view  __________________________________________________

$dlg->window->width(400)->height(300);
$dlg->window->buttons(array(
    Ext::Button()->text('Ok')->handler($dlg->done),
    Ext::Button()->text('Bezár')->handler(new ExtFunction("this.window.close()"))->scope(new ExtCodeFragment("this")),
));

$dlg->add(Ext::Form('Form')
		->border(false)
		->defaults(array("labelWidth" => 170, "anchor" => "100%"))
		->bodyPadding(5)
		->add(
			Ext::NumberField('campaignTypeGroup')
			->value('')
			->allowBlank(false)
			->fieldLabel('Csoport')
		)
);

$dlg->add(Ext::GridPanel("Grid")
		->store($dlg->store("GridStore"))
		->preventHeader(true)
		->collapsible(true)
		->selModel(new ExtCodeFragment("Ext.create('Ext.selection.CheckboxModel', { checkOnly : true })"))
		->height(200)
		->title('Kampány típus(ok)')
		->iconCls('icon-grid')
		->plugins(array(
				new ExtCodeFragment("Ext.create('Ext.ux.grid.plugin.HeaderFilters', { pluginId: 'gridFilters', enableTooltip : false })")
		))
		->autoExpandColumn('name')
)
;

$dlg->add(Ext::Form('HelperForm')
		->border(false)
		->defaults(array("labelWidth" => 170, "anchor" => "100%"))
		->bodyPadding(5)
		->add(
			Ext::Hidden('helperInput')
			->value('')
		)
);


// function implementation ________________________________

$dlg->done->begin()?>
var me = this,
    grid = Ext.getCmp('<?php echo $dlg->Grid->id ?>')
   ;

	var helper = Ext.getCmp('<?php echo $dlg->HelperForm->helperInput->id; ?>');
	var parentGrid = Ext.getCmp(helper.getValue());
    var selModel = grid.getSelectionModel();
	var selected = selModel.getSelection();

	var ctForm = Ext.getCmp('<?php echo $dlg->Form->id; ?>');
	var ctGroup = Ext.getCmp('<?php echo $dlg->Form->campaignTypeGroup->id; ?>');
	var groupValue = ctGroup.getValue();
	
	if(ctForm.getForm().isValid()){
		if(groupValue==''){
			Ext.Msg.alert('Hiba', 'A csoport megadása kötelező!');
		}
		
	    Ext.each(selected, function(model){
	        model.set('campaign_type_group', groupValue);
	    	parentGrid.getStore().add(model);
	   	});
	
	   	me.window.close();
	}

<?php $dlg->done->end();

// template methods _______________________________________

$dlg->beginMethod("initDialog()") ?>
var grid = Ext.getCmp('<?php echo $dlg->Grid->id ?>');
    
var me = this;

var helper = Ext.getCmp('<?php echo $dlg->HelperForm->helperInput->id ?>');
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
