<?php

class ExtModel extends ExtComponent
{
	public function __construct($name = "", $classdesc = "", ExtClass $context = null)
	{
		parent::__construct($name, $classdesc, $context);
		
		$this->extend("Ext.data.Model");
		
		// if fields is array, creating ExtFieldConfig instance of it
		if ($this->fields !== null && !($this->fields instanceof ExtFieldConfig)) {
			$this->fields = new ExtFieldConfig($this->fields, $this);
		}
		// or not exists, create an empty configuration
		elseif ($this->fields === null) {
			$this->fields = new ExtFieldConfig($this);
		}
	}
	
	public function getFieldConfig() { return $this->fields; }
	
	/**
	 * shorthand to getFieldConfig()
	 */
	public function fieldConfig() { return $this->getFieldConfig(); }
		
	/**
	 * add field to field configuration object
	 */
	public function field($name, $type = "", $params = array())
	{
		// method overloading
		if (is_array($type)) {
			$params = $type;
		}
		else {
			$params["type"] = $type;
		}
		
		return $this->addField($name, $params);
	}
	
	protected function addField($name, $params = array())
	{
		// $this->_config["fields"] is ExtFieldConfig instance!
		return $this->fields->addField($name, $params);
	}
		
	protected function createSourceCode($define_variable = false)
	{
		$code = "";
		$code .= "if (!Ext.ModelManager.isRegistered('".$this->_name."')){\n";
		$code .= sprintf("	Ext.define('%s', \n%s\n	);\n", $this->_name, $this->getMembersAsJson());
		$code .= "}\n";
				
		return $code;
	}
	
	public function __get($name)
	{
		$member = parent::__get($name);
		if ($member === null) {
			if (isset($this->_models[$name])) {
				$member = $this->_models[$name];
			}
			elseif (isset($this->_stores[$name])) {
				$member = $this->_stores[$name];
			}
		}
		return $member;
	}
	
	public function ref()
	{
		return new ExtCodeFragment($this->_name,  "Ext.ModelManager.getModel('{$this->_name}')");
	}
	
}