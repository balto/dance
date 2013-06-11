<?php

/**
 * This is the model class for table "member".
 *
 * The followings are the available columns in table 'member':
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $birthdate
 * @property string $address
 * @property string $sex
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * The followings are the available model relations:
 * @property MemberCampaignType[] $memberCampaignTypes
 * @property Ticket[] $tickets
 */
class Member extends CActiveRecord
{
	const MALE_NAME = 'male';
	const FEMALE_NAME = 'female';
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Member the static model class
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
		return 'member';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, sex, created_by, created_at', 'required'),
			array('email','unique'),
			array('email','email'),
			array('created_by, updated_by', 'numerical', 'integerOnly'=>true),
			array('birthdate', 'length', 'max'=>4),
			array('name, email, address', 'length', 'max'=>256),
			array('updated_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, email, birthdate, address, created_by, created_at, updated_by, updated_at', 'safe', 'on'=>'search'),
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
			'memberCampaignTypes' => array(self::HAS_MANY, 'MemberCampaignType', 'member_id'),
			'tickets' => array(self::HAS_MANY, 'Ticket', 'member_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => Yii::t('msg','Név'),
			'email' => Yii::t('msg','E-mail cím'),
			'birthdate' => Yii::t('msg','Születési év'),
			'address' => Yii::t('msg','Cím'),
			'sex' => Yii::t('msg','Neme'),
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('birthdate',$this->birthdate);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('sex',$this->sex,true);
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