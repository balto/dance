// <script type="text/javascript">
<?php

// module definition ______________________________________

$dlg = new ExtDialog($this, array(
    "cacheable" => true,
    "title" => 'Módosítási napló',
    'layout' => 'fit',
));


// functions, event handlers (declarations) ________________


// models and stores ______________________________________

$dlg->createModel("History", $this->listHistoryFieldDefinitions());

// store name => array(model name, autoload)

$dlg->createStore('HistoryStore')
    ->model($dlg->model('History'))
    ->autoLoad(false)
    ->remoteSort(true)
    ->remoteFilter(true)
    ->pageSize($max_per_page) // nem lehet megadni, hogy ne küldje a store a page paramétereket
    ->proxy(Ext::Proxy()->url('member/member/getHistoryList', $this)
        ->reader(Ext::JsonReader())
    );


// view  __________________________________________________




$dlg->window->width(850)->height(550);
$dlg->add(Ext::GridPanel("Grid")
        ->store($dlg->store("HistoryStore"))
        ->preventHeader(true)
        ->bbar(Ext::PagingToolbar())
        ->plugins(array(
        new ExtCodeFragment("Ext.create('Ext.ux.grid.plugin.HeaderFilters', { pluginId: 'gridFilters', enableTooltip : false })")
    ))
)
;
$dlg->window->buttons(array(
    Ext::Button()->text('Bezár')->handler(new ExtFunction("this.window.close()"))->scope(new ExtCodeFragment("this")),
));

// template methods _______________________________________

$dlg->beginMethod("initDialog()") ?>

if (this.params.id) {
    var history_store = this.stores.get('HistoryStore');
    var h_proxy = history_store.getProxy();
    h_proxy.extraParams.id = this.params.id;

    history_store.load();
}

this.callParent(arguments);
return true;
<?php $dlg->endMethod();

$dlg->render();
