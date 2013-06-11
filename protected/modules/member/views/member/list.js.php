// <script type="text/javascript">
<?php

$mdl = new ExtModule($this, array(
    "cacheable" => true,
    "title" => 'Tagok'
));


// member variables______________________________________________

// functions, event handlers (declarations) ________________

// event handlers
$mdl->onMemberdatachanged(Ext::fn("this.stores.get('GridStore').load();"));



// methods
$mdl->createMethod("openRecord(grid, record, action, row, col)");
$mdl->createMethod("addRecord(btn, pressed)", "theApp.showDialog('member/member/show', null, this);");

// models and stores ______________________________________

$mdl->createModel("MemberData", $this->listMemberFieldDefinitions());

$mdl->createStore("GridStore")
    ->model($mdl->model("MemberData"))
    ->autoLoad(false)
    ->remoteSort(true)
    ->remoteFilter(true)
    ->proxy(Ext::Proxy()
        ->url("getList", $this)
        ->reader(Ext::JsonReader())
    )
;

// view  __________________________________________________


$mdl->add(Ext::GridPanel("GridMember")
    ->store($mdl->store("GridStore"))
    ->preventHeader(true)
    ->collapsible(true)
    ->iconCls('icon-grid')
    ->bbar(Ext::PagingToolbar()
        ->add(Ext::ToolbarSeparator())
        ->add(Ext::Button("ButtonNewRecord")
            ->iconCls('icon-add')
            ->text('Új tag')
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
    /*->rowaction(Ext::RowAction("history")
        ->iconCls('icon-folder-find')
        ->qtip('Módosítási napló')
        ->callback($mdl->openHistory)
    )
    ->rowaction(Ext::RowAction("logout")
        ->iconCls('icon-door-in')
        ->qtip('Kiléptetés')
        ->callback($mdl->openLogout)
    )*/
)
;


// function implementation ________________________________

$mdl->openRecord->begin() //(grid, record, action, row, col) ?>
    theApp.showDialog('member/member/show', {
        id: record.data.id
    }, this);
<?php $mdl->openRecord->end();

// template methods _______________________________________

$mdl->beginMethod("initModule()") ?>

    this.stores.get('GridStore').load({
        scope: this,
        callback: function() {
            this.fireEvent('moduleready');
        }
    });

    return false;
<?php
$mdl->endMethod();

$mdl->render();
