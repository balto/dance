<?php

/**
 * This is the model class for table "campaign_type_permission".
 *
 * The followings are the available columns in table 'campaign_type_permission':
 * @property integer $id
 * @property integer $campaign_type_id
 * @property integer $permission_campaign_type_id
 *
 * The followings are the available model relations:
 * @property CampaignType $campaignType
 * @property CampaignType $permissionCampaignType
 */
class CampaignTypePermission extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CampaignTypePermission the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'campaign_type_permission';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('campaign_type_id, permission_campaign_type_id', 'required'),
			array('campaign_type_id, permission_campaign_type_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, campaign_type_id, permission_campaign_type_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'campaignType' => array(self::BELONGS_TO, 'CampaignType', 'campaign_type_id'),
			'permissionCampaignType' => array(self::BELONGS_TO, 'CampaignType', 'permission_campaign_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'campaign_type_id' => 'Campaign Type',
			'permission_campaign_type_id' => 'Permission Campaign Type',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('campaign_type_id',$this->campaign_type_id);
		$criteria->compare('permission_campaign_type_id',$this->permission_campaign_type_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}