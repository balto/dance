<?php

class ExtWidget extends ExtComponent
{
	protected
		$_xtype,
		$_itemsMap;
	
	// Extjs array properties
	protected static $_arrayProperties = array(
		"dockedItems"
	);
	
	public function __construct($name, $classdesc, ExtClass $context = null)
	{		
		parent::__construct($name, $classdesc, $context);
		
		$this->_xtype = ($classdesc!==null) ? $classdesc[ExtClasses::XTYPE] : "";
		
		if ($this->_xtype != "") {
			$this->xtype($this->_xtype);
		}
		if ($this->_name != "") {
			$this->name($this->_name);
			$this->id($this->_name);
		}
		
		$this->_itemsMap = array();
	}
	
	public function id($id)
	{
		if ($id == "") {
			if ($this->_name != "") {
				$id = $this->_name;
			}
			elseif ($this->_xtype != "") {
				$id = strtolower($this->_xtype);
			}
			else {
				$id = strtolower(get_class($this));
			}
			$id = $id.uniqid();
		}
		
		if ($this->_context instanceof ExtModule) {
			$id = $this->_context->getNamespace().".widget.".$id;
		}
		
		$this->id = $id;
		
		return $this;
	}
	
	public function getXtype() { return $this->_xtype; }
	
	public function setContext(ExtClass $context)
	{
		parent::setContext($context);
		
		$this->id($this->_name);
		
		// set all child items's context
		if (is_array($this->items)) {
			foreach ($this->items as $item) {
				if ($item instanceof ExtWidget) {
					$item->setContext($context);
				}
			}
		}
		
		// set all child docked items's context
		if (is_array($this->dockedItems)) {
			foreach ($this->dockedItems as $item) {
				if ($item instanceof ExtWidget) {
					$item->setContext($context);
				}
			}
		}
	}
		
	public function add($name, $widget = null)
	{
		// method overloading
		// one parameter passed, the widget
		if ($name instanceof ExtWidget) {
			$widget = $name;
			$name = $widget->getName();
		}
		// if context not set, set context of this
		if ($widget->getContext() === null) {
			if ($this->_context instanceof ExtModule) {
				$widget->setContext($this->_context);
			}
			else {
				$widget->setContext($this);
			}
		}
				
		if ($this->items === null || !is_array($this->items)) {
			$this->items = array();
		}
				
		// workaround: Indirect modification of overloaded property has no effect 
		$items = $this->items;
		$items[] = $widget;				
		$this->items = $items;
		
		if ($name != "") {
			$this->_itemsMap[$name] = count($this->items)-1;
		}
		
		return $this;
	}
				
	/**
	 * 
	 * @param string|ExtWidget     name or the widget itself to be removed
	 * @oaram boolean              true: return the removed widget, false: return this
	 * @return ExtWidget           reference to removed widget or this widget
	 */
	public function remove($widget, $returnWidget = false)
	{
		$name = "";
		// method overloading
		// passed the widget
		if ($widget instanceof ExtWidget) {
			$name = $widget->getName();
		}
		// passed the widget's name
		elseif (is_string($widget)) {
			$name = $widget;
		}
		else {
			throw new ExtException("Invalid argument: first argument must be string or instance of ExtWidget!");
		}
				
		$widget = null;
		
		if (isset($this->_itemsMap[$name])) {
			$index = $this->_itemsMap[$name];
			$widget = $this->items[$index];
			
			// workaround: Indirect modification of overloaded property has no effect 
			$items = $this->items;
			unset($items[$index]);
			unset($this->_itemsMap[$name]);
			$this->items = $items;
			
		}
		
		if ($returnWidget)
			return $widget;
		else
			return $this;
	}

	/**
	 * 
	 * @param string|ExtWidget     name or the widget itself to be replaced
	 * @oaram ExtWidget            replacement widget
	 * @oaram boolean              true: return the removed widget, false: return this
	 * @return ExtWidget           reference to removed widget or this widget
	 */
	public function replace($search, ExtWidget $replacementWidget, $returnWidget = false)
	{
		// method overloading
		// passed the widget
		if ($search instanceof ExtWidget) {
			$search = $search->getName();
		}
		// passed the widget's name
		elseif (!is_string($search)) {
			throw new ExtException("Invalid argument: first argument must be string or instance of ExtWidget!");
		}
		
		$removedWidget = null;
				
		if (isset($this->_itemsMap[$search])) {
			$index = $this->_itemsMap[$search];
			unset($this->_itemsMap[$search]);
			$removedWidget = $this->items[$index];
			$this->_itemsMap[$replacementWidget->getName()] = $index;
						
			// workaround: Indirect modification of overloaded property has no effect 
			$items = $this->items;
			$items[$index] = $replacementWidget;
			$this->items = $items;			
		}
		
		if ($returnWidget)
			return $removedWidget;
		else
			return $this;
	}
	
	public function __get($name)
	{
		$member = parent::__get($name);
		if ($member === null && isset($this->_itemsMap[$name])) {
			$member = $this->items[$this->_itemsMap[$name]];
		}
		return $member;
	}
	
	/**
	 *  if extjs property is array but no array given, so make it array
	 */
	public function __set($name, $value)
	{
		if (in_array($name, self::$_arrayProperties) && $value instanceof ExtWidget) {
			if (is_object($value) || !array_key_exists(0, $value)) {
				$value = array($value);
			}
		}
		
		return parent::__set($name, $value);
	}
		
	/////////////////////////////////
	// widget configuration helpers
	
	/**
	 * set scope of handler function to the module (this)
	 
	 * @param ExtCodeFragment $handler
	 * @return \ExtWidget 
	 */
	public function handler(ExtCodeFragment $handler)
	{
		parent::scope(new ExtCodeFragment("this"));
		
		return parent::handler($handler);
	}
	
	/**
	 * bbar
	 */
	public function bbar($bbar)
	{
		if ($bbar instanceof ExtComponent) {
			$bbar->dock('bottom');
		}
		
		$docked_items = $this->dockedItems;
		if (!is_array($docked_items)) {
			$docked_items = array();
		}
		$docked_items[] = $bbar;
		
		return $this->dockedItems($docked_items);
	}
	
}
