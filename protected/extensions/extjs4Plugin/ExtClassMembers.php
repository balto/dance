<?php

class ExtClassMembers implements ArrayAccess, Countable, Iterator
{	
	public 
		$_class, // instance of ExtClass class
		$_members; // Extjs component config
	
	protected
		$_i;
		
	public function __construct(ExtClass $class = null)
	{
		$this->_class = $class;
		$this->_members = array();
		$this->_i = 0;
	}
	
	public function getMembers()
	{
		return $this->_members;
	}
	
	public function getAsArray($raw = null)
	{
		if ($raw === null) {
			$raw = $this->_members;
		}
		
		if (!is_array($raw) || empty($raw)) {
			return array();
		}

		$array = array();
		foreach ($raw as $key => $value) 
		{
			if ($value instanceof ExtClass) {
				// if already defined or rendered, we just pass the reference
				if ($value->isDefined() && $value->isRendered() || $value->isRendered()) {
					$value = $value->ref();
				}
				else {
					$value = $value->getMembersAsArray();
				}	
			}
			
			if ($value instanceof ExtConfig) {
				$value = $value->getConfigAsArray();
			}			
			
			if (is_array($value)) {
				$value = $this->getAsArray($value);
			}
			
			$array[$key] = $value;
		}
		
		return $array;
	}
	
	public function getAsJson()
	{
		if (empty($this->_members)) {
			return "{}";
		}
		else {
			return ExtJsonBuilder::build($this->getAsArray());
		}
	}
	
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->_members);
	}
	
	public function offsetGet($offset)
	{
		return $this->_members[$offset];
	}
	
	public function offsetSet($offset, $value)
	{
		$this->_members[$offset] = $value;
	}
	
	public function offsetUnset($offset)
	{
		unset($this->_members[$offset]);
	}
	
	public function count()
	{
		return count($this->_members);
	}
	
	public function current()
	{
		return current($this->_members);
	}
	
	public function key()
	{
		return key($this->_members);
	}
	
	public function next()
	{
		next($this->_members);
		$this->_i++;
	}
	
	public function rewind()
	{
		reset($this->_members);
		$this->_i = 0;
	}

	public function valid()
	{
		return $this->_i < count($this->_members);
	}

	public function __call($method, $args)
	{
		$this->offsetSet($method, $args[0]);
		
		return $this;
	}
	
	public function __get($name)
	{
		return $this->offsetGet($name);
	}
	
	public function __set($name, $value)
	{
		return $this->offsetSet($name, $value);
	}
}