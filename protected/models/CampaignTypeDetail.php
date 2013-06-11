<?php

/**
 * This is the model class for table "campaign_type_detail".
 *
 * The followings are the available columns in table 'campaign_type_detail':
 * @property integer $id
 * @property integer $campaign_type_id
 * @property integer $moment_count
 * @property integer $required_moment_count
 * @property string $required_moments
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * The followings are the available model relations:
 * @property CampaignType $campaignType
 * @property CampaignTypeDetailMoment[] $campaignTypeDetailMoments
 */
class CampaignTypeDetail extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CampaignTypeDetail the static model class
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
		return 'campaign_type_detail';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('campaign_type_id, created_by, created_at', 'required'),
			array('campaign_type_id, moment_count, required_moment_count, created_by, updated_by', 'numerical', 'integerOnly'=>true),
			array('required_moments', 'length', 'max'=>255),
			array('updated_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, campaign_type_id, moment_count, required_moment_count, required_moments, created_by, created_at, updated_by, updated_at', 'safe', 'on'=>'search'),
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
			'campaignTypeDetailMoments' => array(self::HAS_MANY, 'CampaignTypeDetailMoment', 'campaign_type_detail_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'campaign_type_id' => 'Kampány típus',
			'moment_count' => 'Alkalmak száma',
			'required_moment_count' => 'Kötelező alkalmak száma',
			'required_moments' => 'Kötelező alkalmak',
			'created_by' => 'Created By',
			'created_at' => 'Created At',
			'updated_by' => 'Updated By',
			'updated_at' => 'Updated At',
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
		$criteria->compare('moment_count',$this->moment_count);
		$criteria->compare('required_moment_count',$this->required_moment_count);
		$criteria->compare('required_moments',$this->required_moments,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_by',$this->updated_by);
		$criteria->compare('updated_at',$this->updated_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function behaviors(){
		return array(
				'Blameable' => array(
						'class'=>'ext.behaviors.BlameableBehavior',
				),
		);
	}
}