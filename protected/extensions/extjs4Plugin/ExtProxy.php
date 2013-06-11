<?php

class ExtProxy extends ExtClass
{
	/**
	 * url format:
	 * 
	 * action: url is the action in the current controller
	 * module/controller/action: url is full url to module/controller/action
	 */
	public function url($url, $controller = null)
	{
		if (!($controller instanceof CController)) {
			if ($this->_context !== null) {
				$controller = $this->_context->getController();
			}
		}
		
		$url = self::createUrl($url, $controller);
				
		return parent::url($url);
	}
	
	public static function createUrl($url, $controller = null, $create_full_url = true)
	{
		if (strpos($url, "/") === false) {
			if ($controller !== null) {
				$url = $controller->getUniqueId()."/".$url;
			}
			else {
				throw new ExtException("Cannot create URL: context is missing!");
			}
		}
		
		if (!$create_full_url) {
			return $url;
		}
		else {				
			$base = Yii::app()->params["no_script_name"] ? "": $_SERVER["SCRIPT_NAME"];		
			return rtrim($base, "/")."/".$url;
		}
	}
	
}