<?php

/**
 * This is the model class for table "member_campaign_type".
 *
 * The followings are the available columns in table 'member_campaign_type':
 * @property integer $id
 * @property integer $member_id
 * @property integer $campaign_type_id
 * @property string $start_date
 * @property string $end_date
 * @property integer $done
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * The followings are the available model relations:
 * @property Member $member
 * @property CampaignType $campaignType
 */
class MemberCampaignType extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return MemberCampaignType the static model class
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
		return 'member_campaign_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_id, campaign_type_id, start_date, done, created_by, created_at', 'required'),
			array('member_id, campaign_type_id, done, created_by, updated_by', 'numerical', 'integerOnly'=>true),
			array('end_date, updated_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, member_id, campaign_type_id, start_date, end_date, done, created_by, created_at, updated_by, updated_at', 'safe', 'on'=>'search'),
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
			'member' => array(self::BELONGS_TO, 'Member', 'member_id'),
			'campaignType' => array(self::BELONGS_TO, 'CampaignType', 'campaign_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'member_id' => 'Member',
			'campaign_type_id' => 'Kampány típus',
			'start_date' => 'Kezdési dátum',
			'end_date' => 'Teljesítés dátuma',
			'done' => 'Teljesítve',
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
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('campaign_type_id',$this->campaign_type_id);
		$criteria->compare('start_date',$this->start_date,true);
		$criteria->compare('end_date',$this->end_date,true);
		$criteria->compare('done',$this->done);
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