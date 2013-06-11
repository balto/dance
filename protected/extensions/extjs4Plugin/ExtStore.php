<?php

class ExtStore extends ExtComponent
{
	protected $_model;
	
	public function __construct($name, $classdesc, ExtClass $context = null)
	{
		parent::__construct($name, $classdesc, $context);
		
		$this->storeId = $name;
	}
	
	public function getModel() { return $this->_model; }
	
	public function model(ExtModel $model)
	{
		$this->_model = $model;
		$this->sorters(new ExtFieldConfig($model->getFieldConfig()->extractSorters(), $this));
		
		return parent::model($model);
	}
	
	public function defineClass()
	{
		return "";
	}
	
	public function createClass()
	{
		$code = sprintf("Ext.create('%s', \n%s\n)", $this->_classname, $this->getMembersAsJson());
		
		return new ExtCodeFragment($code);
	}
	
	protected function createSourceCode($define_variable = false)
	{
		return $this->createClass();
	}
	
	public function ref()
	{
		return new ExtCodeFragment($this->_name,  "Ext.data.StoreManager.lookup('{$this->storeId}')");
	}
	
}