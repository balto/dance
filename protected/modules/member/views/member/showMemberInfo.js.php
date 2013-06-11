// <script type="text/javascript">
<?php

// module definition ______________________________________

$dlg = new ExtDialog($this, array(
    "cacheable" => true,
    "title" => 'Tag adatai',
    'layout' => 'fit',
));


// functions, event handlers (declarations) ________________

// models and stores ______________________________________


$dlg->createModel("MemberCampaignType", $this->listMemberCampaignTypeFieldDefinitions());

$dlg->createStore("GridStore")
	->model($dlg->model("MemberCampaignType"))
	->autoLoad(false)
	->remoteSort(true)
	->remoteFilter(true)
	->proxy(Ext::Proxy()
		->url("getMemberCampaignTypeList", $this)
		->reader(Ext::JsonReader())
	)
;

// view  __________________________________________________

$dlg->window->width(500)->height(400);
$dlg->window->buttons(array(
    Ext::Button()->text('Bezár')->handler(new ExtFunction("this.window.close()"))->scope(new ExtCodeFragment("this")),
));

$dlg->add(Ext::GridPanel("Grid")
		->store($dlg->store("GridStore"))
		->preventHeader(true)
		->collapsible(true)
		->title('Tag kampány típus(ok)')
		->iconCls('icon-grid')
		->plugins(array(
				new ExtCodeFragment("Ext.create('Ext.ux.grid.plugin.HeaderFilters', { pluginId: 'gridFilters', enableTooltip : false })")
		))
)
;


// function implementation ________________________________


// template methods _______________________________________

$dlg->beginMethod("initDialog()") ?>
this.window.setDisabled(true);
Ext.getCmp('<?php echo $dlg->Grid->id ?>').updateHeader();

if (this.params.id) {
	var store = this.stores.get('GridStore');
	store.getProxy().extraParams.id = this.params.id;
	
	store.load();
	this.window.setDisabled(false);
}


this.callParent(arguments);

<?php $dlg->endMethod();


$dlg->render();