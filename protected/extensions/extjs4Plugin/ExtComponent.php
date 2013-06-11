<?php

class ExtComponent extends ExtClass
{
	/**
	 * Component class already defined in Extjs core
	 */
	public function defineClass()
	{
		return "";
	}
	
	public function createClass()
	{
		$code = sprintf("Ext.create('%s', \n%s\n)", $this->_classname, $this->getMembersAsJson());
		return new ExtCodeFragment($code);
	}	
	
	/**
	 * calling "on<HandlerFunction>()" create a listener and set scope to 'this'
	 *
	 */
	public function __call($method, $arguments)
	{
		if (substr($method, 0, 2) == "on") {
			if (count($arguments) == 0) {
				throw new ExtException("Missing event handler for {$method}!");
			}
			
			$listeners = $this->listeners;
			if (!is_array($listeners)) {
				$listeners = array();
			}
			
			$listeners[strtolower(substr($method, 2))] = $arguments[0];
			// setting scope, if context is ExtModule, set scope to this module
			if (!array_key_exists("scope", $listeners)) {
				$scope = null;
				if ($this->_context instanceof ExtModule) {
					$scope = new ExtCodeFragment("this");
				}
				if (count($arguments) == 2) {
					$scope = $arguments[1];
				}
				if ($scope !== null) {
					$listeners["scope"] = $scope;
				}
			}
						
			return $this->listeners($listeners);
		}
		
		return parent::__call($method, $arguments);
	}
		
}