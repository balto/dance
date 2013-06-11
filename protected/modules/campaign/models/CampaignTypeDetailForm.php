<?php

/**
 */
class CampaignTypeDetailForm extends EFormModel
{
	public $id;
	public $csrf_token;
	public $campaign_type_id;
	public $moment_count;
	public $required_moment_count; 
	public $required_moments;

	protected $name_format = 'CampaignTypeDetail';

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
