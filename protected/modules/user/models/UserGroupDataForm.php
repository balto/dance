<?php

/**
 */
class UserGroupDataForm extends EFormModel
{
    public $id;
    public $csrf_token;
	public $name;
	public $description;

	protected $name_format = 'UserGroup';

	/**
	 * Declares the validation rules.
	 * The rules state that loginname and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(

		);
	}

	public function attributeLabels()
	{
	    return array(
				'name' => Yii::t('msg','Megnevezés'),
				'description' => Yii::t('msg','Leírás'),
	    );
	}

}
