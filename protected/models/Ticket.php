<?php

/**
 * This is the model class for table "ticket".
 *
 * The followings are the available columns in table 'ticket':
 * @property integer $id
 * @property integer $member_id
 * @property integer $ticket_type_id
 * @property integer $price
 * @property integer $payed_price
 * @property string $active_from
 * @property string $active_to
 * @property integer $moment_left
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * The followings are the available model relations:
 * @property Member $member
 * @property TicketType $ticketType
 * @property CampaignTicket[] $campaignTickets
 * @property TicketCampaignMoment[] $ticketCampaignMoments
 */
class Ticket extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Ticket the static model class
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
		return 'ticket';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_id, ticket_type_id, price, payed_price, active_from, active_to, created_by, created_at', 'required'),
			array('id, member_id, ticket_type_id, price, payed_price, moment_left, created_by, updated_by', 'numerical', 'integerOnly'=>true),
			array('updated_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, member_id, ticket_type_id, price, payed_price, active_from, active_to, moment_left, created_by, created_at, updated_by, updated_at', 'safe', 'on'=>'search'),
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
			'campaignTickets' => array(self::HAS_MANY, 'CampaignTicket', 'ticket_id'),
			'member' => array(self::BELONGS_TO, 'Member', 'member_id'),
			'ticketType' => array(self::BELONGS_TO, 'TicketType', 'ticket_type_id'),
			'ticketCampaignMoments' => array(self::HAS_MANY, 'TicketCampaignMoment', 'ticket_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'member_id' => Yii::t('msg', 'Tag'),
			'ticket_type_id' => Yii::t('msg', 'Bérlet típus'),
			'price' => Yii::t('msg', 'Ár'),
			'payed_price' => Yii::t('msg', 'Befizetett ár'),
			'active_from' => Yii::t('msg', 'Mettől'),
			'active_to' => Yii::t('msg', 'Meddig'),
			'moment_left' => Yii::t('msg', 'Felhasználható alkalmak száma'),
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
		$criteria->compare('ticket_type_id',$this->ticket_type_id);
		$criteria->compare('price',$this->price);
		$criteria->compare('payed_price',$this->payed_price);
		$criteria->compare('active_from',$this->active_from,true);
		$criteria->compare('active_to',$this->active_to,true);
		$criteria->compare('moment_left',$this->moment_left);
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