<?php

/**
 */
class PermissionForm extends EFormModel
{
	public $title;
	public $name;
	public $description;


	/**
	 * Declares the validation rules.
	 * The rules state that loginname and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
	    return array(
	        array('title, name, description', 'ESafeValidator'),
	    );
	}


}
