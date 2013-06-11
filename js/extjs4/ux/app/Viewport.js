Ext.define('Ext.ux.app.Viewport', {
	extend: 'Ext.container.Viewport',
	layout: 'border',
	config: {
		background: {
			fit: false,
			tile: false,
			image: ''
		}
	},

	construct: function(config) {
		this.initConfig(config);
		this.callParent(arguments);
	},

	initComponent: function() {		
		Ext.apply(this, {
			items: [{
					id: 'menubar',
					xtype: 'toolbar',
					region: 'north',
					height: 28
				}, {
					id: 'content',
					xtype: 'container',
					margins: '5 5 5 5',
					region: 'center',
					layout: 'card',
					html: this.background.fit ? '<img src="'+this.background.image+'" style="width: 100%;height: 100%;" />' : '',
					style: this.background.tile ? 'background: '+this.background.color+' url('+this.background.image+') repeat; background-position:center;' : 'background: '+this.background.color+' url('+this.background.image+') no-repeat; background-position:center;'
				}, {
					id: 'statusbar',
					xtype: 'toolbar',
					region: 'south',
					height: 28
				}				
			]
		});
		
		this.callParent();
	}

});