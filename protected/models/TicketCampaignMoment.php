<?php

/**
 * This is the model class for table "ticket_campaign_moment".
 *
 * The followings are the available columns in table 'ticket_campaign_moment':
 * @property integer $id
 * @property integer $ticket_id
 * @property integer $campaign_moment_id
 * @property string $moment_datetime
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * The followings are the available model relations:
 * @property Ticket $ticket
 * @property CampaignMoment $campaignMoment
 */
class TicketCampaignMoment extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TicketCampaignMoment the static model class
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
		return 'ticket_campaign_moment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ticket_id, campaign_moment_id, moment_datetime, created_by, created_at', 'required'),
			array('ticket_id, campaign_moment_id, created_by, updated_by', 'numerical', 'integerOnly'=>true),
			array('updated_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, ticket_id, campaign_moment_id, moment_datetime, created_by, created_at, updated_by, updated_at', 'safe', 'on'=>'search'),
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
			'ticket' => array(self::BELONGS_TO, 'Ticket', 'ticket_id'),
			'campaignMoment' => array(self::BELONGS_TO, 'CampaignMoment', 'campaign_moment_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'ticket_id' => 'Ticket',
			'campaign_moment_id' => 'Campaign Moment',
			'moment_datetime' => 'Moment Datetime',
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
		$criteria->compare('ticket_id',$this->ticket_id);
		$criteria->compare('campaign_moment_id',$this->campaign_moment_id);
		$criteria->compare('moment_datetime',$this->moment_datetime,true);
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