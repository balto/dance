<?php

/**
 * This is the model class for table "settlement".
 *
 * The followings are the available columns in table 'settlement':
 * @property integer $id
 * @property string $name
 * @property integer $county_id
 * @property integer $region_id
 * @property string $zip_from
 * @property string $zip_to
 * @property integer $is_active
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * The followings are the available model relations:
 * @property Client[] $clients
 * @property Client[] $clients1
 * @property Client[] $clients2
 * @property County $county
 * @property Region $region
 * @property User $createdBy
 * @property User $updatedBy
 */
class Settlement extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Settlement the static model class
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
		return 'settlement';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, county_id, region_id, zip_from, zip_to, is_active, created_by, created_at', 'required'),
			array('county_id, region_id, is_active, created_by, updated_by', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('zip_from, zip_to', 'length', 'max'=>10),
			array('updated_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, county_id, region_id, zip_from, zip_to, is_active, created_by, created_at, updated_by, updated_at', 'safe', 'on'=>'search'),
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
			'clients' => array(self::HAS_MANY, 'Client', 'birth_place_id'),
			'clients1' => array(self::HAS_MANY, 'Client', 'settlement_id'),
			'clients2' => array(self::HAS_MANY, 'Client', 'notify_settlement_id'),
			'county' => array(self::BELONGS_TO, 'County', 'county_id'),
			'region' => array(self::BELONGS_TO, 'Region', 'region_id'),
			'createdBy' => array(self::BELONGS_TO, 'User', 'created_by'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Megnevezés',
			'county_id' => 'Megye (tartomány)',
			'region_id' => 'Régió',
			'zip_from' => 'Ir.szám -tól',
			'zip_to' => 'Ir.szám -ig',
			'is_active' => 'Aktív',
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
		$criteria->compare('county_id',$this->county_id);
		$criteria->compare('region_id',$this->region_id);
		$criteria->compare('zip_from',$this->zip_from,true);
		$criteria->compare('zip_to',$this->zip_to,true);
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_by',$this->updated_by);
		$criteria->compare('updated_at',$this->updated_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}