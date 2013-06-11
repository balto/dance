Ext.define('Ext.ux.form.SearchField', {
    extend: 'Ext.form.field.Trigger',
    
    alias: 'widget.addfield',
    
    triggerCls: Ext.baseCSSPrefix + 'form-add-trigger',
    
    initComponent: function(){
        this.callParent(arguments);
        this.on('specialkey', function(f, e){
            if(e.getKey() == e.ENTER){
                this.onTriggerClick();
            }
        }, this);
    },
    
    onTriggerClick : Ext.emptyFn
});