/**
* Ext.ux.grid.PageSize
  http://www.sencha.com/forum/showthread.php?130787-Ext.ux.toolbar.PagingOptions/page2
*/
Ext.define('Ext.ux.grid.PageSize', {
    extend      : 'Ext.form.field.ComboBox',
    alias       : 'plugin.pagesize',
    beforeText  : 'Show',
    afterText   : 'rows/page',
    queryMode   : 'local',
    displayField: 'text',
    valueField  : 'value',
    allowBlank  : false,
    triggerAction: 'all',
    width       : 50,
    maskRe      : /[0-9]/,
    position	: 11,
    /**
    * initialize the paging combo after the pagebar is randered
    */
    init: function(paging) {
        if(this.pageSize) {
            paging.store.pageSize = this.pageSize;
            this.setValue(this.pageSize);
        }
        paging.on('afterrender', this.onInitView, this);
    },

    /**
    * create a local store for availabe range of pages
    */
    store: new Ext.data.SimpleStore({
        fields: ['text', 'value'],
        data: [['5', 5], ['10', 10], ['15', 15], ['20', 20], ['25', 25], ['50', 50], ['100', 100], ['200', 200], ['500', 500]]
    }),
    /**
    * assing the select and specialkey events for the combobox
    * after the pagebar is rendered.
    */
    onInitView: function(paging) {
        this.setValue(paging.store.pageSize);
        paging.insert(this.position++, this.beforeText);
        paging.insert(this.position++, this);
        paging.insert(this.position, this.afterText);
        //paging.add('-', this.beforeText, this, this.afterText);
        this.on('select', this.onPageSizeChanged, paging);
        this.on('specialkey', function(combo, e) {
            if(13 === e.getKey()) {
                this.onPageSizeChanged.call(paging, this);
            }
        });
    },
    /**
    * refresh the page when the value is changed
    */
    onPageSizeChanged: function(combo) {
        this.store.pageSize = parseInt(combo.getRawValue(), 10);
        this.moveFirst();
    }
});
