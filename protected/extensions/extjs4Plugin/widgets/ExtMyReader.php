<?php

class ExtMyReader extends ExtReader
{
	public function __construct($name, $classdesc = null, ExtClass $context = null)
	{
		parent::__construct($name, $classdesc, $context);
		
		$this->listeners(array(
				"exception" => new ExtCodeFragment("theApp.handleStoreException")
		));
	}
}
