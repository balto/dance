Ext.define('Ext.ux.app.Dialog', {
	extend: 'Ext.ux.app.Module',
	
	requires: ['Ext.ux.app.DialogWindow'],
	
	parentWindow: null,
	window: null,
	
	constructor: function(config) {
		this.callParent(arguments);
	},
	
	initComponent: function() {
		Ext.applyIf(this, {
			border: false,
			layout: 'fit',
			centered: true,
			autoRender: false,
			// panel body look:
			frame: false,
			header: false,
			bodyCls: 'x-window-body-plain',
			bodyStyle: 'border:none'
		});
		
		this.window = this.createWindow();
		
		this.callParent();
	},
	
	doModal: function(parentWindow) {		
		if (this.window != null) {
			if (parentWindow != null) {
				Ext.apply(this.window, { 
//					animateTarget: parentWindow
				});			
			}
			
			this.parentWindow = parentWindow || null;
			
			this.window.center();
			this.window.show();
		}
		
		return this;
	},
	
	// virtual method, creates dialog main window
	createWindow: function(config) {
		config = config || {};
		// if window is resizable, enable maximization
		if (config.resizable) {
			if (config.maximizable == undefined) config.maximizable = true;
			if (config.pinned == undefined) config.pinned = true;
			if (config.width != undefined) config.minWidth = config.width;
			if (config.height != undefined) config.minHeight = config.height;
		}
		return Ext.create('Ext.ux.app.DialogWindow', Ext.applyIf(config, {
			title: this.title,
			layout: 'fit',
			closeAction: 'destroy',
			items: [this],
			plain : true,
			modal: true,
			resizable: false,
			minimizable: false,
			maximizable: false,
			border: false,
			stateful: false,
			isWindow: true,
			constrainHeader: true,
			autoRender: true,
			hideMode: 'visibility',
			buttons: [{
				text: MESSAGES.BUTTON_CLOSE,
				handler: function() { this.window.close(); },
				scope: this
			}]
		}), this);
	},
			
	/*
	 * Template method, calls the dialog's window before render itself.
	 * 
	 * If dialog is already rendered (cacheable), the onRender parameter's
	 * value will be set to false
	 * 
	 * Return value is irrelevant.
	 */
	initDialog: function(onRender) {
	}
	
});