<?php

class ExtFunction extends ExtCodeFragment
{	
	protected 
		$_parameters,
		$_body;
	
	public function __construct($name = "", $parameters = null, $body = null, ExtClass $context = null)
	{
		// function overloading (not supported at language level)
		// name and body given: (body in parameters)
		if (is_string($parameters) && !is_array($name)) {
			$context = $body;
			$body = $parameters;
			$parameters = array();
			if ($body instanceof Ext || $body instanceof ExtCodeFragment) {
				$body = $body->render(false);
			}
		}
		// parameters and body given
		elseif (is_array($name)) {
			$body = $parameters;
			$parameters = $name;
			$name = "";
		}
		// one parameter given, treat as body or parameters
		elseif ($parameters === null && $body === null) {
			if (is_array($name)) {
				$parameters = $name;
				$body = "";
			}
			else {
				$parameters = array();
				$body = $name;
				$name = "";
			}
		}
		
		if (!is_array($parameters)) {
			$parameters = array();
		}
		$this->_parameters = $parameters;
		$this->_body = $body;
		
		parent::__construct($name, "", $context);
	}
	
	protected function createSourceCode()
	{
		$code = "";
		$code .= sprintf("function(%s) {\n%s\n}", implode(",", $this->_parameters), $this->_body);
		
		return $code;
	}
	
	protected function create($body = "")
	{
		$this->_body = $body;
		
		return $this;
	}
	
	public function append($code)
	{
//		$return = "";
//		$return_pos = mb_strpos($body, "return", 0, "UTF-8");
//		if ($return_pos !== false) {
//			$return = mb_substr($body, $return_pos, mb_strpos($body, ";", $return_pos, "UTF-8")-$return_pos).";";
//			$body = str_replace($return, "", $body);
//		}
		if ($this->_body == "") {
			$this->_body = rtrim($code, ";\n\r").";\n";
		}
		else {
			$this->_body = (rtrim($this->_body, ";\n\r").";\n".rtrim($code, ";\n\r").";\n");
		}
	}
	
	public function appendBefore($code)
	{
		if (trim($code) == "") {
			return;
		}
		
		if ($this->_body == "") {
			$this->_body = rtrim($code, ";\n\r").";\n";
		}
		else {
			$this->_body = (rtrim($code, ";\n\r").";\n".rtrim($this->_body, ";\n\r").";\n");
		}
	}
	
	public function begin()
	{
		ob_start();
		return $this;
	}
	
	public function end()
	{
		$body = ob_get_clean();
		
		if ($this->_body == "") {
			$this->create($body);
		}
		else {
			$this->append($body);
		}

		if ($this->_context instanceof Ext && $this->_context->isAutoRender()) {
			$this->render();
		}
		
		return $this;
	}
	
	public function getParameters() { return $this->_parameters; }
	
	public function call()
	{
		$args = func_get_args();
		if (count($args) > count($this->_parameters)) {
			throw new ExtException("Too many function parameters!");
		}
		
		for ($i=0; $i<count($args); $i++) {
			$args[$i] = json_encode($args[$i]);
		}
		
		if (!$this->isRendered()) {
			if ($this->_context instanceof Ext && $this->_context->isAutoRender()) {
				$this->render();
			}
		}
		
		if ($this->_context !== null && $this->_context->getNamespace() != "") {
			return new ExtCodeFragment(null, $this->_context->getNamespace().".".$this->_name."(".implode(",", $args).")\n", $this->getContext());
		}
		else {
			return new ExtCodeFragment(null, $this->_name."(".implode(",", $args).")\n", $this->getContext());
		}
	}	
	
	public function getSourceCode()
	{
		return $this->createSourceCode();
	}
	
	public function render($echo_result = true)
	{
		$result = "";
		
		if ($this->getName() != "") {
			$result .= sprintf("%s = ", $this->getName());
			if ($echo_result) {
				echo $result;
			}
		}

		$result .= parent::render($echo_result);
		
		if (!$echo_result) {
			return $result;
		}
	}	
}