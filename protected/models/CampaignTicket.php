<?php

/**
 * This is the model class for table "campaign_ticket".
 *
 * The followings are the available columns in table 'campaign_ticket':
 * @property integer $id
 * @property integer $campaign_id
 * @property integer $ticket_id
 * @property integer $created_by
 * @property string $created_at
 *
 * The followings are the available model relations:
 * @property Campaign $campaign
 * @property Ticket $ticket
 */
class CampaignTicket extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CampaignTicket the static model class
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
		return 'campaign_ticket';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('campaign_id, ticket_id', 'required'),
			array('campaign_id, ticket_id, created_by', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, campaign_id, ticket_id, created_by, created_at', 'safe', 'on'=>'search'),
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
			'campaign' => array(self::BELONGS_TO, 'Campaign', 'campaign_id'),
			'ticket' => array(self::BELONGS_TO, 'Ticket', 'ticket_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'campaign_id' => 'Campaign',
			'ticket_id' => 'Ticket',
			'created_by' => 'Created By',
			'created_at' => 'Created At',
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
		$criteria->compare('campaign_id',$this->campaign_id);
		$criteria->compare('ticket_id',$this->ticket_id);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('created_at',$this->created_at,true);

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