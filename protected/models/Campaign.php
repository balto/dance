<?php

/**
 * This is the model class for table "campaign".
 *
 * The followings are the available columns in table 'campaign':
 * @property integer $id
 * @property integer $campaign_type_detail_id
 * @property integer $location_id
 * @property string $start_datetime
 * @property string $end_datetime
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * The followings are the available model relations:
 * @property CampaignTypeDetail $campaignTypeDetail
 * @property Location $location
 * @property CampaignMoment[] $campaignMoments
 */
class Campaign extends CActiveRecord
{
	
	const INCOME_NAME = 'income';
	const EXPENSE_NAME = 'expense';
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Campaign the static model class
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
		return 'campaign';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('campaign_type_detail_id, location_id, start_datetime, created_by, created_at', 'required'),
			array('campaign_type_detail_id, location_id, created_by, updated_by', 'numerical', 'integerOnly'=>true),
			array('end_datetime, updated_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, campaign_type_detail_id, location_id, start_datetime, end_datetime, created_by, created_at, updated_by, updated_at', 'safe', 'on'=>'search'),
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
			'campaignTypeDetail' => array(self::BELONGS_TO, 'CampaignTypeDetail', 'campaign_type_detail_id'),
			'location' => array(self::BELONGS_TO, 'Location', 'location_id'),
			'campaignMoments' => array(self::HAS_MANY, 'CampaignMoment', 'campaign_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'campaign_type_detail_id' => Yii::t('msg', 'Kampány típus fajta'),
			'location_id' => Yii::t('msg', 'Hely'),
			'start_datetime' => Yii::t('msg', 'Kezdési dátum'),
			'end_datetime' => Yii::t('msg', 'Befejezés dátum'),
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
		$criteria->compare('campaign_type_detail_id',$this->campaign_type_detail_id);
		$criteria->compare('location_id',$this->location_id);
		$criteria->compare('start_datetime',$this->start_datetime,true);
		$criteria->compare('end_datetime',$this->end_datetime,true);
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