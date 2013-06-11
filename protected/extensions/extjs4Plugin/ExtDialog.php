<?php

class ExtDialog extends ExtModule
{
	public
		$window;
	
	public function __construct($controller, $config = array())
	{
		parent::__construct($controller, $config);
		
		$this->window = new ExtClassMembers($this);
	}
	
	// add button to window
	public function button(ExtWidget $buttons) 
	{
		$buttons = $this->window->buttons;
		if (!is_array($buttons)) {
			$buttons = array();
		}
		$buttons[] = $button;
		$this->window->buttons($buttons);
		
		return $this->window;
	}
	
	public function buttons(array $buttons)
	{
		$this->window->buttons($buttons);
	}
	
	// set window configuration from array (old school)
	public function window(array $config)
	{
		foreach ($config as $key => $value) {
			$this->window->$key($value);
		}
	}
	
	protected function renderInternal()
	{
		// override virtual createWindow() method to configure the window object
		$this->createMethod("createWindow()", sprintf("return this.callParent([{%s}]);\n", ExtJsonBuilder::build($this->window->getAsArray(), ExtJsonBuilder::BUILD_AS_RAW)));
		
		return parent::renderInternal();
	}	
}