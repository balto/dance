String.prototype.format = function() {
  var args = arguments;
  return this.replace(/{(\d+)}/g, function(match, number) { 
    return typeof args[number] != 'undefined'
      ? args[number]
      : match
    ;
  });
};

Ext.onReady(function() {

	if(Ext.ux.DateTimePicker) {
		Ext.override(Ext.ux.DateTimePicker,{
            fillDateTime: function(value) {
                if(this.timefield) {
                    var rawtime = this.timefield.getRawValue();
                    value.setHours(rawtime.h);
                    value.setMinutes(Math.floor(rawtime.m / 15)*15);
                    value.setSeconds(0);
                }
                return value;
            },
            selectToday: function() {
                var me = this,
                    btn = me.todayBtn,
                    handler = me.handler;

                if(btn && !btn.disabled) {
                    // me.setValue(Ext.Date.clearTime(new Date())); //src
                    me.setValue(new Date());// overwrite: fill time before setValue
                    me.value = me.fillDateTime(me.value);
                    me.fireEvent('select', me, me.value);
                    if(handler) {
                        handler.call(me.scope || me, me, me.value);
                    }
                    me.onSelect();
                }
                return me;
            }
		});
	}

	if(Ext.ux.form.TimePickerField) {
		Ext.override(Ext.ux.form.TimePickerField,{
            onRender: function() {
                this.callParent(arguments);

                this.spinners[2].hide();
            }
		});
	}

	if(Ext.ux.form.DateTimeField) {
		Ext.override(Ext.ux.form.DateTimeField, {
			getSubmitData: function() {
				var obj = {};
				obj[this.name] = 'Date('+this.getRawValue()+')';
				return obj;
			}
		});
	}
	
});

Ext.override(Ext.panel.Panel,{
   initComponent: function() {
        var me = this,
            cls;

        me.addEvents(

            /**
             * @event beforeclose
             * Fires before the user closes the panel. Return false from any listener to stop the close event being
             * fired
             * @param {Ext.panel.Panel} panel The Panel object
             */
            'beforeclose',

            /**
             * @event beforeexpand
             * Fires before this panel is expanded. Return false to prevent the expand.
             * @param {Ext.panel.Panel} p The Panel being expanded.
             * @param {Boolean} animate True if the expand is animated, else false.
             */
            "beforeexpand",

            /**
             * @event beforecollapse
             * Fires before this panel is collapsed. Return false to prevent the collapse.
             * @param {Ext.panel.Panel} p The Panel being collapsed.
             * @param {String} direction . The direction of the collapse. One of
             *
             *   - Ext.Component.DIRECTION_TOP
             *   - Ext.Component.DIRECTION_RIGHT
             *   - Ext.Component.DIRECTION_BOTTOM
             *   - Ext.Component.DIRECTION_LEFT
             *
             * @param {Boolean} animate True if the collapse is animated, else false.
             */
            "beforecollapse",

            /**
             * @event expand
             * Fires after this Panel has expanded.
             * @param {Ext.panel.Panel} p The Panel that has been expanded.
             */
            "expand",

            /**
             * @event collapse
             * Fires after this Panel hass collapsed.
             * @param {Ext.panel.Panel} p The Panel that has been collapsed.
             */
            "collapse",

            /**
             * @event titlechange
             * Fires after the Panel title has been set or changed.
             * @param {Ext.panel.Panel} p the Panel which has been resized.
             * @param {String} newTitle The new title.
             * @param {String} oldTitle The previous panel title.
             */
            'titlechange',

            /**
             * @event iconchange
             * Fires after the Panel iconCls has been set or changed.
             * @param {Ext.panel.Panel} p the Panel which has been resized.
             * @param {String} newIconCls The new iconCls.
             * @param {String} oldIconCls The previous panel iconCls.
             */
            'iconchange'
        );

        // Save state on these two events.
        this.addStateEvents('expand', 'collapse');

        if (me.unstyled) {
            me.setUI('plain');
        }

        if (me.frame) {
            me.setUI(me.ui + '-framed');
        }

        // Backwards compatibility
        me.bridgeToolbars();

        me.callParent();
        me.collapseDirection = me.collapseDirection || me.headerPosition || Ext.Component.DIRECTION_TOP;
        
        if(typeof me.store != 'undefined'){
            me.store.addListener('beforeload', function(){ this.setLoading(true); }, me);
            me.store.addListener('load', function(){ this.setLoading(false); }, me);
        }
    }
});

Ext.define('Ext.ux.form.override.MultiSelect', {
    override : 'Ext.ux.form.MultiSelect',

    setupItems : function() {
        var me = this;
        me.boundList = Ext.create('Ext.view.BoundList', {
            deferInitialRefresh : false,
            multiSelect : true,
            store : me.store,
            displayField : me.displayField,
            disabled : me.disabled
        });
        me.boundList.getSelectionModel().on('selectionchange', me.onSelectChange, me);
        //START OVERRIDE
        this.selectedPanel = new Ext.panel.Panel({
            bodyStyle : 'border: 0;',
            layout : 'fit',
            title : me.title,
            tbar : me.tbar,
            items : me.boundList
        });

        return {
            xtype : 'container',
            layout : 'fit',
            items : this.selectedPanel
        };
    },
    getSubmitValue: function() {
        var me = this,
            delimiter = me.delimiter,
            val = me.getValue();

        return typeof val == 'undefined' ? '' : (Ext.isString(delimiter) ? val.join(delimiter) : val); 
    }
    
});

Ext.define('Ext.ux.form.override.ItemSelector', {
    override : 'Ext.ux.form.ItemSelector',

    fromTitle : 'Available',
    toTitle : 'Selected',

    setupItems : function() {
        var items = this.callParent();

        this.fromField.selectedPanel.setTitle(this.fromTitle);
        this.toField.selectedPanel.setTitle(this.toTitle);

        return items;
    },
    setValue: function(value){
        var me = this,
            fromStore = me.fromField.store,
            toStore = me.toField.store,
            selected;

        // Wait for from store to be loaded
        if (!me.fromField.store.getCount() && me.fromField.store.data.generation == 0) {
            me.fromField.store.on({
                load: Ext.Function.bind(me.setValue, me, [value]),
                single: true
            });
            return;
        }
        value = me.setupValue(value);
        me.mixins.field.setValue.call(me, value);

        selected = me.getRecordsForValue(value);

        Ext.Array.forEach(toStore.getRange(), function(rec){
            if (!Ext.Array.contains(selected, rec)) {
                // not in the selected group, remove it from the toStore
                toStore.remove(rec);
                fromStore.add(rec);
            }
        });
        toStore.removeAll();

        Ext.Array.forEach(selected, function(rec){
            toStore.add(rec);
        });
       
        Ext.Array.forEach(selected, function(rec){
            // In the from store, move it over
            if (fromStore.indexOf(rec) > -1) {
                fromStore.remove(rec);     
            }
        });
    }
    
});

Ext.override(Ext.window.Window, {
    setActive: function(active, newActive) {
        var me = this;


        if (active) {
            if (me.el.shadow && !me.maximized) {
                me.el.enableShadow(true);
            }
//            if (me.modal && !me.preventFocusOnActivate) {
//                me.focus(false, true);
//            }
            me.fireEvent('activate', me);
        } else {
            // Only the *Windows* in a zIndex stack share a shadow. All other types of floaters
            // can keep their shadows all the time
            if (me.isWindow && (newActive && newActive.isWindow)) {
                me.el.disableShadow();
            }
            me.fireEvent('deactivate', me);
        }
    }
});


Ext.define('Wy.form.field.Base',{
    override : 'Ext.form.field.Base',
    afterLabelTextTpl: new Ext.XTemplate('<tpl if="allowBlank===false"><span class="required-flag" data-qtip="Kötelező kitölteni">*</span></tpl>', { disableFormats: true })
});

Ext.define('Wy.form.FieldContainer',{
    override : 'Ext.form.FieldContainer',
    afterLabelTextTpl: new Ext.XTemplate('<tpl if="allowBlank===false"><span class="required-flag" data-qtip="Kötelező kitölteni">*</span></tpl>', { disableFormats: true })
});

Ext.define('Wy.form.HtmlEditor',{
    override : 'Ext.form.HtmlEditor',
    afterLabelTextTpl: new Ext.XTemplate('<tpl if="allowBlank===false"><span class="required-flag" data-qtip="Kötelező kitölteni">*</span></tpl>', { disableFormats: true })
});

Ext.define('Ext.ux.grid.override.PageSize', {
    override: 'Ext.ux.grid.PageSize',
    reset: function() {
        this.setValue(this.store.pageSize);
    }
});

Ext.chart.theme.White = Ext.extend(Ext.chart.theme.Base, {
    constructor: function() {
        Ext.chart.theme.White.superclass.constructor.call(this, {
            axis: {
                stroke: 'rgb(8,69,148)',
                'stroke-width': 1
            },
            baseColor: '#56C'
        });
    }
});

Ext.form.RadioGroup.override({
    setValue: Ext.Function.createSequence(
        Ext.form.RadioGroup.prototype.setValue,
        function(value){
            if(!Ext.isObject(value) && !Ext.isEmpty(this.name)){
                var radios = Ext.form.RadioManager.getWithValue(this.name, value);
                radios.each(function(cb) {
                    cb.setValue(true);
                });
            }
        }
    )
});