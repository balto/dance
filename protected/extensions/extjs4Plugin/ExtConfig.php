<?php

class ExtConfig implements ArrayAccess, Countable, Iterator
{	
	public 
		$_context, // instance of ExtClass class
		$_config; // Extjs component config
	
	protected
		$_i; 
		
	public function __construct(ExtClass $context = null)
	{
		$this->_context = $context;
		$this->_config = array();
		$this->_i = 0;
	}
	
	public function getConfigAsArray($raw = null)
	{
		if ($raw === null) {
			$raw = $this->_config;
		}
		
		if (!is_array($raw) || empty($raw)) {
			return array();
		}

		$config = array();
		foreach ($raw as $key => $value) 
		{
			if ($value instanceof ExtConfig) {
				$value = $value->getConfigAsArray();
			}
			
			if ($value instanceof ExtClass) {
				// if already defined, we just pass the reference
				if ($value->isDefined() && $value->isRendered() || $value->isRendered()) {
					$config[$key] = $value->ref();
				}
				else {
					$config[$key] = $value->getConfig()->getConfigAsArray();
				}	
			}
			else {
				if (is_array($value)) {
					$config[$key] = $this->getConfigAsArray($value);
				}
				else {
					$config[$key] = $value;
				}
			}
		}
		return $config;
	}
	
	public function getConfigAsJson()
	{
		if (empty($this->_config)) {
			return "{}";
		}
		else {
			return ExtJsonBuilder::build($this->getConfigAsArray());
		}
	}
	
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->_config);
	}
	
	public function offsetGet($offset)
	{
		return $this->_config[$offset];
	}
	
	public function offsetSet($offset, $value)
	{
		$this->_config[$offset] = $value;
	}
	
	public function offsetUnset($offset)
	{
		unset($this->_config[$offset]);
	}
	
	public function count()
	{
		return count($this->_config);
	}
		
	public function current()
	{
		return current($this->_config);
	}
	
	public function key()
	{
		return key($this->_config);
	}
	
	public function next()
	{
		next($this->_config);
		$this->_i++;
	}
	
	public function rewind()
	{
		reset($this->_config);
		$this->_i = 0;
	}

	public function valid()
	{
		return $this->_i < count($this->_config);
	}
	
	public function __call($method, $args)
	{
		$this->_config[$method] = $args[0];
		
		return $this;
	}
}