<?php

/**
 */
class TicketForm extends EFormModel
{
	public $id;
	public $csrf_token;
	public $member_id;
	public $ticket_type_id;
	public $price;
	public $payed_price;
	public $active_from;
	public $active_to;
	public $moment_left;

	protected $name_format = 'Ticket';

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
