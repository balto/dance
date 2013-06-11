<?php

/**
 */
class CodetableForm extends EFormModel
{
	public $id;
	public $csrf_token;
	public $name;
	public $is_active;

	protected $name_format = 'Codetable';

	/**
	 * Declares the validation rules.
	 * The rules state that loginname and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			array('name', 'CRequiredValidator'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'name'  => 'Megnevezés',
			'is_active'=> 'Aktív',
		);
	}

}
