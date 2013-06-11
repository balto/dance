<?php

/**
 */
class LocationForm extends EFormModel
{
	public $id;
	public $csrf_token;
	public $name;
	public $address;
	public $is_active;

	protected $name_format = 'Location';

	/**
	 * Declares the validation rules.
	 * The rules state that loginname and password are required,
	 * and password needs to be authenticated.
	 */
    /*
	public function rules()
	{
		return array(
			array('name, status', 'required'),
		);
	}
*/
	/**
	 * Declares attribute labels.
	 */
	/*public function attributeLabels()
	{
		return array_merge(parent::attributeLabels(),
            array(
                
		    )
        );
	}*/

}
