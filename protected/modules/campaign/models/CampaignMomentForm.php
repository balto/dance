<?php

/**
 */
class CampaignMomentForm extends EFormModel
{
	public $id;
	public $csrf_token;
	public $campaign_id;
	public $campaign_type_moment_id;
	public $moment_datetime;

	protected $name_format = 'CampaignMoment';

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
