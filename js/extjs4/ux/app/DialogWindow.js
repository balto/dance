Ext.define('Ext.ux.app.DialogWindow', {
	extend: 'Ext.window.Window',
	
	dialog: null,
	created: false,
	
	constructor: function(config, dialog) {
		this.callParent(arguments);
		
		this.dialog = dialog;
		this.on('beforeclose', function() {
		 this.beforeClose();
		 return false;
		}, this);

		// itt meg nincs renderelve a dialogus panel
		this.on('beforeshow', function() {
			if (this.dialog != null && this.dialog.rendered) {
				this.dialog.initDialog(false);
			}			
			return true;
		});
		
		// ekkora mar a dialogus panel renderve van
		this.on('show', function() {
			if (!this.created && this.dialog != null && this.rendered) {
				this.created = true;
				this.dialog.initDialog(true);
			}
			return true;
		});
		
	},
		
	beforeClose: function() {
		var me = this;
		if (this.dialog != null) {
			this.dialog.unload(function(unload) {
				if (unload) {
					if (me.dialog.cacheable) {
						me.hide();
					}
					else {
						me.destroy();
					}
				}
			});
		}
		return false;
	}
		
});
