// <script type="text/javascript">
<?php

// module definition ______________________________________

$dlg = new ExtDialog($this, array(
    "cacheable" => false,
    "title" => 'Jutalék felosztás',
    'layout' => 'anchor',
));


// functions, event handlers (declarations) ________________
$dlg->onDatachanged(Ext::fn("this.stores.get('TreeStore').load();"));

$dlg->createMethod("editUserRecord()");
$dlg->createMethod("editGeneralRecord()");

/*
$dlg->createMethod("addPermissionRecord(btn, pressed)", "this.showPermissionSelect()");
$dlg->createMethod("removePermissionRecord(grid, record, action, row, col)");
*/
// models and stores ______________________________________

$dlg->createModel("BasicTree", $this->getRulesTreeFieldDefinitions());


$dlg->createStore("TreeStore","TreeStore")
	->model($dlg->model("BasicTree"))
	->autoLoad(false)
	->proxy(Ext::Proxy()
		->url("getCampaignPriceRulesList", $this)
		->reader(Ext::JsonReader())
		->extraParams(array('campaignId' => $campaignId))
	)
;

// view  __________________________________________________

$dlg->window->width(500)->height(440);
$dlg->window->buttons(array(
    //Ext::Button()->text('Mentés')->handler($dlg->save),
    Ext::Button()->text('Bezár')->handler(new ExtFunction("this.window.close()"))->scope(new ExtCodeFragment("this")),
));


$dlg->add(Ext::TreePanel('CampaignTreePanel')
	->store($dlg->store("TreeStore"))
	->rootVisible(false)
	->border(false)
	->defaults(array("labelWidth" => 170, "anchor" => "100%"))
	->bodyPadding(5)
	->columns(array(
		array(
			'xtype' => 'treecolumn',
            'text' => 'Continents -> Regions -> Countries',
            'flex' =>  2,
            'sortable' => true,
            'dataIndex' => 'text'
		),
		array(
            'text' => 'Jutalék',
            'flex' =>  2,
            'renderer' => new ExtCodeFragment('function(value,c,record){
            	var ret = ""; 

				console.log(record);
            	if(value!=null && value!=""){
            		ret = value+" Ft";
				}else if(record.data.percent != null && record.data.percent!=""){
					ret = record.data.percent+" %";
				}else{
					ret = "";
				} 
				
				return ret;
			}'),
            'dataIndex' => 'price'
		),
		array(
			'text' => 'Összeg',
			'flex' =>  2,
			'renderer' => new ExtCodeFragment('function(value,c,record){
            	return value+" Ft";
			}'),
			'dataIndex' => 'full_price',
		)
		
	))
	->height(300)
	->bbar(Ext::Toolbar()
        ->add(Ext::Button("ButtonNewUserRecord")
            ->iconCls('icon-add')
            ->text('Új felhasználó')
            ->handler($dlg->editUserRecord))
		->add(Ext::ToolbarSeparator())
		->add(Ext::Button("ButtonNewGeneralRecord")
            ->iconCls('icon-add')
            ->text('Új általános')
            ->handler($dlg->editGeneralRecord))
	)
	);


// function implementation ________________________________
$dlg->editUserRecord->begin() //(grid, record, action, row, col) ?>
    var campaignTreePanel = Ext.getCmp('<?php echo $dlg->CampaignTreePanel->id ?>');
    var selModel = campaignTreePanel.getSelectionModel();
	var selected = selModel.getSelection();

	if(!selected.length){
		Ext.Msg,alert('Hiba!', 'Válassz egy ágat!');
	}
	else{
		theApp.showDialog('campaign/campaign/showCampaignPriceUser', {
	        parent_id: selected[0].data.id
	    }, this);
	}
<?php $dlg->editUserRecord->end();

$dlg->editGeneralRecord->begin() //(grid, record, action, row, col) ?>
    var campaignTreePanel = Ext.getCmp('<?php echo $dlg->CampaignTreePanel->id ?>');
    var selModel = campaignTreePanel.getSelectionModel();
	var selected = selModel.getSelection();

	if(!selected.length){
		Ext.Msg,alert('Hiba!', 'Válassz egy ágat!');
	}
	else{
		theApp.showDialog('campaign/campaign/showCampaignPriceGeneral', {
	        parent_id: selected[0].data.id
	    }, this);
	}
<?php $dlg->editGeneralRecord->end();

// template methods _______________________________________

$dlg->beginMethod("initDialog()") ?>

this.window.setDisabled(true);

var me = this;

var campaignTreePanel = Ext.getCmp('<?php echo $dlg->CampaignTreePanel->id ?>');

if (this.params.id) {
	//campaignTreePanel.getStore().getProxy().extraParams.campaignId = this.params.id;
	//campaignTreePanel.getStore().load();
	me.window.setDisabled(false);

}
this.callParent(arguments);

<?php $dlg->endMethod();


$dlg->beginMethod("onRender()"); // Ha már tényleg megjelent a dialógus a DOM-ban, akkor lehet KeyMap-et tenni rá ?>
var me = this;

this.callParent(arguments);
<?php $dlg->endMethod();


$dlg->render();