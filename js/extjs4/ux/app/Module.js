Ext.define('Ext.ux.app.Module', {
	extend: 'Ext.panel.Panel',
	
	config : {
		cacheable: false,
		name: ''
	},

	layout: 'fit',
	items: [],

	changed: false,
	app: null,
	params: null,
	classId: null,
	models: null,
	stores: null,
	credentials: null,
	activated: false,
	
	constructor: function(config, classId, app, params) {
		this.classId = classId;
		this.app = app;
		this.params = params;
		
		this.models = new Ext.util.MixedCollection;
		this.stores = new Ext.util.MixedCollection;
				
		this.callParent(arguments);
	},
	
	init: function(params) {
		this.params = params;
	},
	
	initComponent: function() {
		var me = this;
		// card layout event
		this.on('beforeactivate', function() {
			if (this.activated == false && this.rendered) {
				return me.initModule(false);
			}						
			else {
				return !this.rendered || this.activated;
			}
		}, this);

		// card layout event
		this.on('deactivate', function() {
			this.activated = false;
		}, this);
		
		// custom event, should be fired the initModule() method if necessary
		this.on('moduleready', function() {
			this.activated = true;
			this.app.fireEvent('activatemodule', this);
		}, this);
				
		this.callParent();
	},
	
	onRender: function() {
		if (this.initModule(true)) {
			this.app.fireEvent('moduleready', this);
		}
		this.callParent();
	},
			
	/*
	 * Template method, calls on rendering or the card layout activate event
	 * 
	 * If module is already rendered (cacheable), the onRender parameter's
	 * value will be set to false
	 * 
	 * If returns false, must explicitly fire the moduleready event,
	 * otherwise the event automativcally fired to set module active
	 */
	initModule: function(onRender) {
		return true;
	},
		
	unload: function(onUnload) {
		var me = this;
		if (this.changed) {
			Ext.Msg.show({
				title: MESSAGES.CONFIRM_EXIT_TITLE,
				msg: MESSAGES.CONFIRM_EXIT_MSG,
				buttons: Ext.Msg.YESNO, 
				fn: function(buttonId) { 
					if (buttonId == 'yes') {
						onUnload(true);
					}
					else {
						onUnload(false);
					}
				},
				animateTarget: me,
				icon: Ext.window.MessageBox.QUESTION
			});
		}
		else {
			onUnload(true);
		}
	}

});
