<?php

/**
 */
class TicketTypeForm extends EFormModel
{
	public $id;
	public $csrf_token;
	public $moment_count;
	public $is_daily; 
	public $valid_days;

	protected $name_format = 'TicketType';

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
	public function attributeLabels()
	{
		return array_merge(parent::attributeLabels(),
            array(
                
		    )
        );
	}

}
