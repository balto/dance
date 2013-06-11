<?php

class ExtRowActions extends ExtWidget
{
	public function action($action, $config = array())
	{
		if ($action instanceof ExtClass) {
			if ($this->actions === null || !is_array($this->actions)) {
				$this->actions = array();
			}

			// Bug #39449 	Overloaded array properties do not work correctly
			// workaround:
			$actions = $this->actions;
			$actions[] = $action;
			$this->actions = $actions;
		}
		elseif (is_string($action)) {
			$ra = Ext::RowAction($action);
			if (!empty($config)) {
				foreach ($config as $key => $value) {
					$ra->$key($value);
				}
			}
			
			$this->action($ra);
		}
		
		return $this;
	}
}