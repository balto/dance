<?php

class ExtModule extends ExtWidget
{
	protected 
		$_controller, // Yii CController instance
		$_namespace,
		$_credentials,
		$_config,
		$_models,
		$_stores;
	
	public
		$view; // module view configuration
	
	public function __construct($controller, $config = array())
	{
		$this->_namespace = Yii::app()->name.".module.".str_replace("/", ".", $controller->getRoute());
		if (isset($config["cacheable"]) && !$config["cacheable"]) {
			$this->_namespace = uniqid($this->_namespace);
		}
		
		$classname = str_replace("Ext", "", get_class($this));
		$name = implode("", array_map("ucfirst", explode(".", $this->_namespace.".".$classname)));
		
		parent::__construct($name, array(
			ExtClasses::EXTCLASS => $this->_namespace.".".$classname,
			ExtClasses::XTYPE => null
		));
		
		
		$config = array_merge(array(
			"id" => $this->_namespace.'Module',
			"name" => $this->getName(),
			"title" => "",
			"cacheable" => false,
			"credentials" => null,
			"extend" => "Ext.ux.app.".$classname
		), $config);
		
		// credentials, config should not include the config
		$this->_credentials = $config["credentials"]; unset($config["credentials"]);
		
		$this->_config = $config;
		$this->_controller = $controller;
		$this->_models = array();
		$this->_stores = array();
		
		// Extjs class members and configuration:
		$this->extend = $config["extend"]; unset($config["extend"]);
		$this->initModule = new ExtFunction("initModule", null, "return this.callParent(arguments);");
	}
	
	public function getNamespace() { return $this->_namespace; }
	
	/**
	 * Az Ext.ux.app.Application showDialog() metodusanak adhato dialogus
	 * azonoitot keszit (tulajdonkeppen module/contorller/action utvonal)
	 */
	public function getDialogId($action = "", $controller = null)
	{
		if ($controller === null) {
			$controller = $this->_controller;
		}
		
		if ($action == "") {
			$action = $controller->action->id;
		}
		
		return ExtProxy::createUrl($controller->getUniqueId()."/".$action, $controller, false);
	}
	
	///////////////////////////////
	// stores and models
	
	public function createModel($name, $field_config = null)
	{
		$extname = $this->_namespace.".model.".$name;
		
		$this->_models[$name] = new ExtModel($extname, array(
			ExtClasses::EXTCLASS => $extname,
			ExtClasses::DEFAULT_CONFIG => array("fields" => $field_config)
		), $this);
		
		return $this->_models[$name];
	}
	
	/**
	 * shorthand to getModel()
	 */
	public function model($name)
	{
		return $this->getModel($name);
	}
	
	public function getModel($name)
	{
		if(isset($this->_models[$name])) {
			return $this->_models[$name];
		}
		else {
			return null;
		}
	}
	
	public function createStore($name, $type = 'Store')
	{
		$extname = $this->_namespace.".store.".$name;
		
		$store = Ext::$type($extname, $this);
		// config
		$store->module = new ExtCodeFragment("this");
		$store->credentialsReq = $this->_credentials;
		$this->_stores[$name] = $store;
				
		return $this->_stores[$name];
	}
	
	/**
	 * shorthand to getStore()
	 */
	public function store($name)
	{
		return $this->getStore($name);
	}
	
	public function getStore($name)
	{
		if(isset($this->_stores[$name])) {
			return $this->_stores[$name];
		}
		else {
			return null;
		}
	}
	
	/**
	 * Call the specified method, or create/set the specified member
	 * 
	 */			
	public function defineClass()
	{
		$code = "";
		$code .= sprintf("Ext.define('%s', {\n", $this->_classname);
		$code .= sprintf("%s\n", $this->renderInternal());
		$code .= sprintf("})");
		
		return $code;
	}
	
	public function createClass()
	{		
		return sprintf("Ext.create('%s', {\n%s\n})", $this->_classname, ExtJsonBuilder::build($this->_config, ExtJsonBuilder::BUILD_AS_RAW));
	}
		
	protected function createSourceCode($define_variable = false)
	{
		$code = "";
		$code .= "(function() {\n";
		$code .= $this->defineClass();
		if ($code != "") $code .= ";\n";
		
		$code .= sprintf("return {classname: '%s', config: {\n%s\n}};\n", $this->_classname, ExtJsonBuilder::build($this->_config, ExtJsonBuilder::BUILD_AS_RAW));
		
		$code .= "})();\n";
				
		return $code;
	}
	
	
	protected function renderInternal()
	{		
		$code = "";
		
		// override init() method, to add stores and/or models
		if ($this->getMember("initComponent") === null) {
			$this->createMethod("initComponent()", "this.callParent();\n");
		}
		$init = "";
		// models
		foreach ($this->_models as $name => $model) {
			$init .= $model->render(false).";\n";
			$init .= sprintf("this.models.add('%s', Ext.ModelManager.getModel('%s'));\n", $name, $model->getName());
		}
		// stores
		foreach ($this->_stores as $name => $store) {
			$init .= $store->render(false).";\n";
			$init .= sprintf("this.stores.add('%s',Ext.data.StoreManager.lookup('%s'));\n", $name, $store->getName());
		}
		// items
		$items = isset($this->_members["items"])?$this->_members["items"]:array();
		unset($this->_members["items"]); // ne akarja ujra kirenderelni
		$init .= sprintf("Ext.apply(this, { items: %s });", ExtJsonBuilder::build($items, ExtJsonBuilder::BUILD_AS_ARRAY));
		
		$this->initComponent->appendBefore($init);
		
		// render class members
		foreach ($this->_members as $name => $m) {
			if ($code != "") {
				$code .= ",\n";
			}
		
			if ($m instanceof ExtCodeFragment) {
				$code .= sprintf("		%s: %s", $m->getName(), $m->getSourceCode());
			}
			else {
				$code .= sprintf("		%s: %s", $name, ExtJsonBuilder::build($m));
			}
		}
		
		return $code;
	}	
	
}