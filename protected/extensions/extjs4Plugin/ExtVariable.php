<?php

class ExtVariable extends ExtCodeFragment
{	
	protected 
		$_value;
	
	public function __construct($name, $value = null, ExtClass $context = null)
	{
		$this->_value = $value;
		
		parent::__construct($name, "", $context);
	}
	
	protected function createSourceCode($define_variable = false)
	{
		if ($define_variable && $this->_name) {
			return sprintf("var %s = %s", $this->_name, json_encode($this->_value));
		}
		else {
			return sprintf("%s", json_encode($this->_value));
		}
	}
	
	public function getValue() { return $this->_value; }
	
	public function val() { return new ExtCodeFragment(json_encode($this->_value)); }
	
	public function renderAsJson()
	{
		return ExtJsonBuilder::buildAttribute($this->_name, $this->_value);
	}
			
}