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

$dlg->createMethod("addUserRecord()");
$dlg->createMethod("addGeneralRecord()");

$dlg->createMethod("editRecord()");
$dlg->createMethod("deleteRecord()");

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

$dlg->window->width(650)->height(450);
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
            'text' => 'Kampány',
            'flex' =>  2,
            'sortable' => true,
            'dataIndex' => 'text'
		),
		array(
            'text' => 'Jutalék',
            'flex' =>  1,
            'renderer' => new ExtCodeFragment('function(value,c,record){
            	var ret = ""; 

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
			'flex' =>  1,
			'renderer' => new ExtCodeFragment('function(value,c,record){
            	return (record.data.price_type=="expense") ? (record.data.expense_price==null) ? "" : record.data.expense_price+" Ft" : value+" Ft";
			}'),
			'dataIndex' => 'full_price',
		),

		
	))
	->height(390)
	->bbar(Ext::Toolbar()
        ->add(Ext::Button("ButtonNewUserRecord")
            ->iconCls('icon-add')
            ->text('Új felhasználó')
            ->handler($dlg->addUserRecord))
		
		->add(Ext::Button("ButtonNewGeneralRecord")
            ->iconCls('icon-add')
            ->text('Új általános')
            ->handler($dlg->addGeneralRecord))
		
		->add(Ext::ToolbarSeparator())
		
		->add(Ext::Button("ButtonEditRecord")
            ->iconCls('icon-edit-record')
            ->text('Szerkesztés')
            ->handler($dlg->editRecord))
			
		->add(Ext::Button("ButtonDeleteRecord")
	        ->iconCls('icon-remove')
	        ->text('Törlés')
	        ->handler($dlg->deleteRecord))
	)
	);


// function implementation ________________________________
$dlg->addUserRecord->begin() //(grid, record, action, row, col) ?>
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
<?php $dlg->addUserRecord->end();

$dlg->addGeneralRecord->begin() //(grid, record, action, row, col) ?>
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
<?php $dlg->addGeneralRecord->end();

$dlg->editRecord->begin() //(grid, record, action, row, col) ?>
    var campaignTreePanel = Ext.getCmp('<?php echo $dlg->CampaignTreePanel->id ?>');
    var selModel = campaignTreePanel.getSelectionModel();
	var selected = selModel.getSelection();
    
    var type = (selected[0].data.link_id == null) ? 'showCampaignPriceGeneral' : 'showCampaignPriceUser' ;

	if(!selected.length){
		Ext.Msg,alert('Hiba!', 'Válassz egy ágat!');
	}
	else{
		theApp.showDialog('campaign/campaign/' + type, {
	        id: selected[0].data.id
	    }, this);
	}
<?php $dlg->editRecord->end();

$dlg->deleteRecord->begin(); ?>
	var campaignTreePanel = Ext.getCmp('<?php echo $dlg->CampaignTreePanel->id ?>');
    var selModel = campaignTreePanel.getSelectionModel();
	var selected = selModel.getSelection();
	
	var me = this;
	Ext.Msg.show({
		title: '<?php echo Yii::t('msg', "Törlés") ?>',
		msg: '<?php echo Yii::t('msg', "Biztosan törli <b>{0}</b> ?") ?>'.format(selected[0].data.text),
		buttons: Ext.Msg.YESNO,
		fn: function(buttonId) {
			if (buttonId=='yes') {
				Ext.Ajax.request({
					url: '<?php  echo ExtProxy::createUrl("deletePriceRuleRecord", $this) ?>',
					method: 'POST',
					params: {
						price_rule_id: selected[0].data.id
					},
					success: function(r) {
						var response = Ext.decode(r.responseText);
						var error = response.error;
						if (error==0) {
							me.stores.get('TreeStore').load();
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

<?php $dlg->deleteRecord->end();

$dlg->deleteRecord->begin() //(grid, record, action, row, col) ?>
    


	/*if(!selected.length){
		Ext.Msg,alert('Hiba!', 'Válassz egy ágat!');
	}
	else{
		theApp.showDialog('campaign/campaign/showCampaignPriceGeneral', {
	        id: selected[0].data.id
	    }, this);
	}*/
<?php $dlg->deleteRecord->end();

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