<?php

class ExtClass extends ExtCodeFragment
{
	protected
		$_classname,
		$_members; // instance of ExtClassMembers
	
	private
		$_lastMemberAdded;

	public function __construct($name, $classdesc = null, ExtClass $context = null)
	{
		parent::__construct($name, "", $context);
		
		$this->_members = new ExtClassMembers($this);
		$this->_lastMemberAdded = null;
		
		if ($classdesc !== null) {
			$this->_classname = $classdesc[ExtClasses::EXTCLASS];
			if (isset($classdesc[ExtClasses::DEFAULT_CONFIG])) {
				foreach ($classdesc[ExtClasses::DEFAULT_CONFIG] as $name => $value) {
					$this->$name($value);
				}
			}
		}
	}
	
	public function getClassname() { return $this->_classname; }	
	
	public function getMembersAsArray() { return $this->_members->getAsArray(); }
	
	public function getMembersAsJson() { return $this->_members->getAsJson(); }
	
	public function getMember($name)
	{
		if (isset($this->_members[$name])) {
			return $this->_members[$name];
		}
		else {
			return null;
		}
	}
	
	public function addMember(ExtCodeFragment $member, $overwrite = false) 
	{
		$name = $member->getName();
		if ($name != "") {
			if (!$overwrite && $this->getMember($name)) {
				throw new ExtException(sprintf("Member %s already exists!", $name));
			}
			
			$this->_members[$name] = $member;
			$this->_lastMemberAdded = $member;
		}
		else {
			throw new ExtException("Missing member name!");
		}
		
		return $this;
	}
	
	protected function createSourceCode($define_variable = false)
	{
		$code = "";
		$code .= $this->defineClass();
		if ($code != "") $code .= ";\n";
		if ($define_variable && $this->_name) {
			$code .= sprintf("var %s = ", $this->_name);
		}
		$code .= $this->createClass();
				
		return $code;
	}
	
	public function defineClass()
	{
		$code = sprintf("Ext.define('%s', \n%s\n)", $this->_classname, $this->getMembersAsJson());
		return new ExtCodeFragment($code);
	}
	
	public function createClass()
	{
		$code = "";
		$parameters = func_get_args();
		if (empty($parameters)) {
			$code = sprintf("Ext.create('%s')", $this->_classname);
		}
		else {
			$code = sprintf("Ext.create('%s', %s)", $this->_classname, json_encode($parameters));
		}
		return new ExtCodeFragment($code);
	}
	
	/**
	 * Starts function declaration. Use in conjunction ExtClass::endFunction().
	 * 
	 * @param string   function name
	 * @return ExtFunction  function being created
	 */
	public function beginMethod($name)
	{
		if ($name == "") {
			throw new ExtException("Function name is mandatory!");
		}
		
		$function = call_user_func_array(array($this, "createMethod"), array($name, "", null, $this));
				
		$function->begin();
				
		return $function;
	}
	
	/**
	 * Finish function declaration. Use in conjunction ExtClass::beginFunction().
	 * 
	 * @return ExtFunctiun  function declared
	 */
	public function endMethod()
	{
		$function = $this->_lastMemberAdded;
		$function->end();
		return $function;
	}
		
	public function createMethod()
	{
		$args = func_get_args();
		$name = "";
		$parameters = array();
		$body = "";
		
		// anonymous funcion, only function body supplied,
		// or if function exists with given name, return function instance
		if (count($args) > 0) {
			// first parameter is the function body
			if (strpos($args[0], ".") !== false) {
				$body = $args[0];
			}
			else {
				$name_and_parameters = explode("(", $args[0]);
				$name = $name_and_parameters[0];
				// ha csak parameterek vannak ,-vel elvalasztva, akkor
				// az nem a fuggveny neve, hanem a parameterei
				if (strpos($name, ",") !== false) {
					$name_and_parameters[1] = $name;
					$name = "";
				}
				$parameters = array();
				if (count($name_and_parameters) > 1) {
					// truncate ')' end of the parameter string
					if (strrpos($name_and_parameters[1], ")") == strlen($name_and_parameters[1])-1) {
						$name_and_parameters[1] = substr($name_and_parameters[1], 0, -1);
					}
					$parameters = array_map("trim", explode(",", trim($name_and_parameters[1])));
				}
			}
		}
		
		if (count($args) > 1) {
			$body = $args[1];
		}
		
		
		$method = new ExtFunction($name, $parameters, $body, $this);		

		$this->addMember($method, true);
		
		return $method;
	}
	
	/**
	 * shorthand to createVariable()
	 * 
	 * @return ExtVariable  variable declared
	 */
	public function v()
	{
		return call_user_func_array(array($this, "createVariable"), func_get_args());
	}
	
	/**
	 * creates variable
	 * 
	 * @return ExtVariable
	 */
	public function createVariable()
	{
		$args = func_get_args();
		$name = $args[0];
		$value = null;
		
		if (count($args) > 1) {
			$value = $args[1];
		}
		
		$variable = new ExtVariable($name, $value);
		
		$this->addMember($variable, true);
		
		return $variable;
	}
	
	public function def($name)
	{
		$member = new ExtCodeFragment($name, null, $this);
		
		$this->addMember($member, true);
		
		ob_start();
	}
	
	public function endDef()
	{
		$code = ob_get_clean();
		if ($this->_lastMemberAdded instanceof ExtCodeFragment) {
			$this->addMember(new ExtCodeFragment($this->_lastMemberAdded->getName(), $code, $this), true);
		}
	}
	
	
	/**
	 * Shorthand to getMember()
	 */
	public function __get($name)
	{
		return $this->getMember($name);
	}	
	
	public function __set($name, $value)
	{
		$this->_members[$name] = $value;
				
		return $this;
	}
		
	/**
	 * Call the specified method, or create/set the specified member
	 * 
	 */
	public function __call($method, $arguments)
	{
		if ($this->getMember($method) instanceof ExtFunction) {
			if ($this->getName() !== "") {
				if ($this->isDefined()) {
					return new ExtCodeFragment(sprintf("%s.%s(%s)", $this->_name, $method, json_encode($arguments)));
				}
				else {
					throw new ExtException(sprintf("Class %s is not defined!", $this->_name));
				}
			}
			else {
				return new ExtCodeFragment(sprintf("this.%s(%s)", $method, json_encode($arguments)));
			}
		}
		// called as $this->member(value)
		elseif (count($arguments) == 1) {
			return $this->__set($method, $arguments[0]);
		}
		// called as $this->member(name, value)
		elseif (count($arguments) == 2) {
			$m = $this->getMember($method);
			if ($m === null) {
				$m = array();
			}
			if (!is_array($m)) {
				throw new ExtException(sprintf("Invalid assigment (%s(%s))!", $method, implode(",", $arguments)));
			}
			$m[$arguments[0]] = $arguments[1];
			$this->__set($method, $m);
		}
		else {
			throw new ExtException(sprintf("Invalid assigment (%s(%s))!", $method, implode(",", $arguments)));
		}
	}
}