<?php

class PayInForm extends EFormModel{
	public $id; //ticket_id
	public $csrf_token;
	public $price;
	
	protected $name_format = 'PayIn';
	
	public function attributeLabels()
	{
		return array(
			'price' => 'Összeg'
		);
	}
}
