Ext.define('Ext.ux.app.Application', {
	mixins: {
		observable: 'Ext.util.Observable'
	},
	
	requires: [
		'Ext.ux.app.Viewport',
		'Ext.ux.app.Module',
		'Ext.ux.app.Dialog'
	],
	
	config: {
		systemBaseUrl: '',
		userAuthenticated: false,
		loginUrl: '',
		defaultLanguage: ''
	},

	appName: '',
	modules: new Ext.util.MixedCollection,
	currentModule: null,
	viewport: null,
	clientView: null,

	constructor: function(appName) {
		this.appName = appName || '';
		this.mixins.observable.constructor.call(this);
	},

	initApp: function(config) {

		this.viewport = Ext.create('Ext.ux.app.Viewport', config);
		this.clientView = this.viewport.items.get('content');
		
		this.initConfig(config);
		
		this.on('activatemodule', function(module) {	
			this.clientView.getLayout().setActiveItem(module);
		}, this);
		
		if (this.userAuthenticated == true) {
			this.initMenuBar();
			this.initStatusBar();
		}
		else {
			this.showLoginDialog();
		}		
		
		Ext.tip.QuickTipManager.init();
	},
		
	initMenuBar: function() {
		var me = this;
		this.loadModuleClass('/menu/default/getUserMenu', null, function(params, module) {
			return false;
		});
	},
	
	initStatusBar: function () {
		var me = this;
		this.loadModuleClass('/menu/default/getUserStatusBar', null, function(params, module) {
			return false;
		});
	},
	
	
//////////////////////////////////////////////////////
// login/logout
	
	showLoginDialog: function() {
		this.showDialog(this.loginUrl, this.defaultLanguage);
	},
	
	doLogin: function() {
		this.userAuthenticated = true;
		this.initMenuBar()
		this.initStatusBar()
	},
	
	doLogout: function() {
		Ext.Ajax.request({
			url: this.getSystemBaseUrl()+'/logout',
			success: function(r) {
				var response = Ext.JSON.decode(r.responseText);
				if (response.redirect) {
					Ext.getDoc().dom.location.href = response.redirect;
				} else {
					Ext.getDoc().dom.location.href = this.getSystemBaseUrl;
				}
			}
		});
	},
	
	// template method
	onAfterModuleLoaded: function(module) {
		if (module.title != '') {
			// set browser title
			Ext.getDoc().dom.title = this.appName+'-'+module.title;
		}
	},
	
	showDialog: function(classId, params, parentWindow) {
		var me = this;
		params = params || {};
		var module = this.modules.get(classId);
		var onModuleCreated = function(module, params) {			
			if (module != null) {
				module.init(params);
				module.doModal(parentWindow || null);
			}			
		};
		
		if (module != null) {
			onModuleCreated(module, params);
		}
		else {
			this.loadModuleClass(classId, params, onModuleCreated);
		}
	},
	
	loadModule: function(classId, params) {
		var me = this;
		
		// callback function to createModule method
		var onModuleCreated = function(module, params) {
			if (module != null) {
				module.init(params);
				me.clientView.getLayout().setActiveItem(module);
				if (me.currentModule != null && !me.currentModule.cacheable) {
					me.clientView.items.remove(me.currentModule);
					me.currentModule.destroy();
					delete me.currentModule;
				}
				me.currentModule = module;
				me.onAfterModuleLoaded(module);
			}
		}

		var module = this.modules.get(classId);
		if (this.currentModule != null) {
			if (this.currentModule.classId == classId) {
				return true;
			}
			
			this.currentModule.unload(function(unload) {				
				if (unload == true) {
					if (module != null) {
						onModuleCreated(module, params);
					}
					else {
						me.loadModuleClass(classId, params, onModuleCreated);
					}			
				}
			});
			
			return false;
		}
		else {
			if (module != null) {
				onModuleCreated(module, params);
			}
			else {
				// asynchronous call, return value irrelevant
				this.loadModuleClass(classId, params, onModuleCreated);
			}
		}
		
		return true;
	},
	
	
////////////////////////////////////////////////////////////
/// private methods

	createModule: function(createOptions, onModuleCreated) {
		var me = this;
		if (createOptions.classname) {
			var module = Ext.create(createOptions.classname, 
				createOptions.config || {},
				createOptions.classId || {},
				this,
				createOptions.params || {}
			);
				
			if (module != null) {
				if (me.modules.get(createOptions.classId) == null && createOptions.config.cacheable == true) {
					me.modules.add(createOptions.classId, module);
				}
				
				if (typeof onModuleCreated == 'function') {
					onModuleCreated(module, createOptions.params);
				}
			}
		}
		else {
			me.runtimeError("Falied to create module:<br /><br />missing classname");
		}
	},
	
	/*
	 * - classId must be the path (URL) of a Yii module/controller/action
	 * - if class loaded, calls onModuleCreated callback it with module instance
	 * 
	 *  @return void
	 */
	loadModuleClass: function(classId, params, onModuleCreated) {
		var me = this;
		
		// classId is URL
		if (classId.indexOf('/') != -1) {
			var url = this.getSystemBaseUrl() + '/' + classId;
			Ext.Ajax.request({
				url: url,
				method: 'POST',
				params: params,
				timeout: 300*1000,
				text: MESSAGES.LOADING,
				scope: this,
				success: function(r, o) {
					if (r.responseText && r.responseText.charAt(0) != '{' && r.responseText.charAt(1) != '[') {
						var createOptions = window.eval(r.responseText);
						if (createOptions != undefined) {
							if (createOptions.classname) {
								createOptions.classId = classId;
								createOptions.params = params;
								if (typeof onModuleCreated == 'function') {
									me.createModule(createOptions, onModuleCreated);
								}
								else {
									me.runtimeError("Cannot create module object:<br /><br />missing onModuleClassLoaded callback")
								}
							}
							else {
								me.runtimeError('Response not seems to be an application module!');
							}
						}
						else {
							me.runtimeError("Falied to create module:<br /><br />missing classname");
						}
					}
					else {
						me.handleFailure(r, o);
					}
				},
				failure: me.handleFailure,
				callback: function(options, success, response) {
					// not implemented yet : if (slow_script) this.loadMask.hide();
				}
			});
		}
		else {
			this.runtimeError('Module classId must be a valid module/controller/action path.');
		}
	},
	
	handleFailure: function(r, o, callback) {
		if (typeof callback != 'function') callback = Ext.emptyFn;
		if (typeof r == 'undefined') {
			Ext.Msg.alert(MESSAGES.ERROR+' (00) ' + Ext.Date.format(new Date(), 'Y-m-d H:i:s'), MESSAGES.ERROR_DATA);
			return false; 
		}

		if ((typeof r.responseText != 'undefined') && r.responseText) {
			try {
				var resp = Ext.JSON.decode(r.responseText);
				if ((typeof resp != 'undefined') && (typeof resp.status != 'undefined') && resp.status) {
					r.status = resp.status;
				}
			} 
			catch(e) {
				
			}
		}

		if (typeof r.status == 'undefined') {
			Ext.Msg.alert(MESSAGES.ERROR+' (01) {0}'.format(Ext.Date.format(new Date(), 'Y-m-d H:i:s')), MESSAGES.ERROR_DATA);
			return false; 
		}
		
		if (typeof r.responseText == 'undefined') {
			Ext.Msg.alert(MESSAGES.ERROR+' (02)") ?> s: {0} {1}'.format(r.status,Ext.Date.format(new Date(), 'Y-m-d H:i:s')), MESSAGES.ERROR_DATA);
			return false; 
		}
		
		if (r.responseText == '')  {
			r.responseText = '{}';
		}
		switch (r.status) {
			case 401:
				var response = Ext.JSON.decode(r.responseText);
				var url = this.getSystemBaseUrl() + (response.redirect ? ('/' + response.redirect) : '');
				Ext.Msg.show({
					title: MESSAGES.LOGIN_TIMEOUT_TITLE,
					msg: '<div style="width:400px;">'+MESSAGES.LOGIN_TIMEOUT_MSG+'</div>',
					buttons: Ext.Msg.OK,
					fn: function(buttonId) {
						if (buttonId=='ok') {
							Ext.getDoc().dom.location.href = url;
						}
					},
					icon: Ext.MessageBox.WARNING
				});
				break;
				
		case 403:
			var response = Ext.JSON.decode(r.responseText);
			Ext.Msg.alert(MESSAGES.INVALID_REQUEST, response.error.message);
			break;
			
		case 500:
			var title = '500 Internal Server Error';
			if (r.responseText && r.responseText != '{}') {
				theApp.showError500Window(title, r.responseText);
			}
			else {
				Ext.Msg.alert('{0} (500) {1}'.format(title, Ext.Date.format(new Date(), 'Y-m-d H:i:s')), MESSAGES.ERROR_DATA);
			}
			break;
			
		case 200:
			var result = Ext.JSON.decode(r.responseText, true);
			if (result && result.errors) {
				this.handleSuccessFailure(result);
			}
			else {
				var message = (result && result.error && result.error.message) ? result.error.message : MESSAGES.ERROR_DATA;
				Ext.Msg.alert('Hiba (200) ' + Ext.Date.format(new Date(), 'Y-m-d H:i:s'), '<div style="width:400px;">'+message+'</div>', callback);
			}
			break;
			
		default:
			Ext.Msg.alert(MESSAGES.ERROR_REQUEST, 'Státusz kód: {0}<br />{1}'.format(r.status, r.statusText));
		}
	},
	
	handleSuccessFailure: function(result) {
		var error_message = '<br /><br />';
		var parseError = function(errors, prefix) {
			Ext.each(errors, function(error) {
				if (error.errors) {
					parseError(error.errors, '<b>'+error.field+'</b> / ');
				} 
				else {
					if (error.field && error.message) {
						error_message += prefix+error.field+': '+error.message+'<br />';
					} 
					else {
						error_message += error+'<br />';
					}
				}
			});
		}
		parseError(result.errors, '');
		Ext.Msg.alert(MESSAGES.ERROR_FORM, '<div style="width:400px;">'+result.message+error_message+'</div>');
	},
	
	handleFormFailure: function(f, a, callback) {
		theApp.handleFailure(a.response, a.options, callback);
	},
	
	handleStoreException: function(proxy, response, operation, options) {
		theApp.handleFailure(response, options);
	},
	
	showError500Window: function(title, template) {
		var iframe = new Ext.Component({
			autoEl: {		
				tag: 'iframe'
			},
			listeners: {
				afterrender: function() {
					try {
						var doc = iframe.el.dom.contentWindow.document;
						doc.open();
						doc.write(template);
						doc.close();
						return true;
					} catch (e) {
						if (console) console.log(e);
					}
				}
			}
		});
		var window = new Ext.window.Window({
			title: title,
			buttons: [{ text:'OK', handler: function(){this.ownerCt.ownerCt.close();} }],
			width: 500,
			height: 300,
			layout: 'fit',
			buttonAlign: 'center',
			items: iframe
		});
		window.show();
	},
	
	runtimeError: function(message) {
		Ext.Msg.show({
			title: this.appName,
			msg: 'Runtime error<br /><br />'+message,
			icon: Ext.Msg.ERROR,
			buttons: Ext.Msg.OK
		})
	},
	
	loadJS: function(url) {
		var script = document.createElement('script');
		script.setAttribute('src', url);
		script.setAttribute('type','text/javascript');
		document.getElementsByTagName('head')[0].appendChild(script);
	},
	
	handleFormSubmitFailure: function(form, action) {
        switch (action.failureType) {
            case Ext.form.Action.CLIENT_INVALID:
                Ext.Msg.alert(MESSAGES.ERROR, MESSAGES.ERROR_FORM);
                break;
            case Ext.form.Action.CONNECT_FAILURE:
                if (typeof action.response != 'undefined') {
                    theApp.handleFailure(action.response, null);
                }	else {
                    Ext.Msg.alert(MESSAGES.ERROR, MESSAGES.ERROR_CONNECTION);
                }
                break;
            case Ext.form.Action.SERVER_INVALID:
                theApp.handleFormFailure(form, action);
        }
    }
});
