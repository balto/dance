/**
 *  kiegeszites a modul credential kezelesevel
 */
Ext.define('Ext.ux.app.Store', {
	extend: 'Ext.data.Store',
	
	module: null,
	credentialsReq: null,
	
	constructor: function(config)	{
		this.callParent(arguments);		
		this.module = config.module || null;
		this.credentialsReq = config.credentialsReq || null;
		
		if (this.module != null && this.credentialsReq != null && this.module.credentials == null) {
			this.on('load', function(store) {
				if (store.proxy.getReader().rawData && typeof store.proxy.getReader().rawData.credentials != 'undefined') {
					this.module.credentials = store.proxy.getReader().rawData.credentials;
				}
			});
		}
	},
	
	load: function(options) {		
		options = options || {};
		if (typeof options == 'function') {
			options = {
				callback: options
			};
		}		
		
		if (this.module != null && this.credentialsReq != null) {
			options.params = options.params || {};
			Ext.applyIf(options.params, {
				'credentials[]' : this.credentialsReq
			});
		}
		return this.callParent([options]);
	}
});
