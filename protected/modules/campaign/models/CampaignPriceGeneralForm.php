<?php

/**
 */
class CampaignPriceGeneralForm extends EFormModel
{
	public $id;
	public $csrf_token;
	public $tree_parent_id;
	public $name;
	public $price;
	public $percent;
	public $price_type;

	protected $name_format = 'CampaignPriceGeneral';

	/**
	 * Declares the validation rules.
	 * The rules state that loginname and password are required,
	 * and password needs to be authenticated.
	 */
    
	public function rules()
	{
		return array(
			array('name, price_type', 'required'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array_merge(parent::attributeLabels(),
            array(
                'name' => 'Megnevezés',
                'price'   => 'Összeg',
                'percent' => 'Százalék',
                'price_type' => 'Összeg típus',
		    )
        );
	}

}
