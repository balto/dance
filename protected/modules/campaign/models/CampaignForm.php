<?php

/**
 */
class CampaignForm extends EFormModel
{
	public $id;
	public $csrf_token;
	public $campaign_type_detail_id;
	public $location_id;
	public $start_datetime;
	public $end_datetime;

	protected $name_format = 'Campaign';

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
