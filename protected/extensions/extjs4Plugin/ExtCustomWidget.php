<?php

/**
 * all of custom widgets's base class under the /widgets directory
 */
abstract class ExtCustomWidget extends ExtWidget
{
	public function __construct($name, $classdesc, ExtClass $context = null, $initParams = null)
	{		
		parent::__construct($name, $classdesc, $context);
		
		if ($initParams !== null && method_exists($this, "setup")) {
			call_user_func_array(array($this, "setup"), $initParams);
		}
		
		$this->init();
	}

	protected abstract function init();
	
	/**
	 * variadic function
	 * 
	 * called after instantiating the object and before init()
	 */
	public function setup() {}
}
	
