<?php

class ExtCodeFragment
{
	protected 
		$_code,
		$_name,
		$_rendered,
		$_context,
		$_defined;
	
	public function __construct($name = "", $code = null, ExtClass $context = null)
	{
		// method overloading (not supported in PHP at language level)
		// pass only one argument: treat as code
		if ($code === null && $context === null) {
			$code = $name;
			$name = "";
		}
		// pass two arguments: code and context
		elseif ($context === null && $code instanceof ExtClass) {
			$context = $code;
			$code = $name;
			$name = "";
		}
		
		if (is_object($code)) {
			throw new ExtException("Code as object is not supported!");
		}
		$this->_name = $name;
		$this->_code = $code;
		$this->_context = $context;
		$this->_rendered = false;
		$this->_defined = $context instanceof ExtClass;
	}
	
	public function setContext(ExtClass $context)
	{
		$this->_context = $context;
		$this->_defined = true;
	}
	
	public function getSourceCode($define_variable = false)
	{
		return $this->createSourceCode($define_variable);
	}
	
	protected function createSourceCode($define_variable = false)
	{
		return $this->_code;
	}
	
	public function ref()
	{
		if ($this->isDefined()) {
			return new ExtCodeFragment($this->_name, "this.".$this->_name, $this->_context);			
		}
		else {
			return new ExtCodeFragment($this->_name, $this->_name, $this->_context);
		}
	}
	
	public function getName() { return $this->_name; }
	public function getCode() { return $this->_code; }
	public function isRendered() { return $this->_rendered; }
	public function getContext() { return $this->_context; }
	public function isDefined() { return $this->_defined; }
	public function isAnonymous() { return $this->_name == ""; }	
	
	public function render($echo_result = true)
	{
		$this->_rendered = true;
		
		if (false !== ($code = $this->getSourceCode(true))) {
			$this->_code = $code;
		}
		
		$result = $this->_code;		
		
		if ($echo_result) {
			echo $result;
		}
		else {
			return $result;
		}
	}

	public function __toString()
	{
		return $this->getSourceCode();
	}
}