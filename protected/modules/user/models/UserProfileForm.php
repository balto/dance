<?php

/**
 */
class UserProfileForm extends EFormModel
{
	public $tel;
	public $mobil;
	public $email;
	public $id;

	protected $name_format = 'Profile';

	/**
	 * Declares the validation rules.
	 * The rules state that loginname and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
	    // CEmailValidator ezt a regexp mintát használja: $pattern='/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/';
	    // A HEmailValidator pedig: '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i'
		return array(
		    array('email', 'HEmailValidator', 'allowEmpty'=>true),

		);
	}


}
