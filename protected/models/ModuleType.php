<?php

/**
 * This is the model class for table "module_type".
 *
 * The followings are the available columns in table 'module_type':
 * @property integer $id
 * @property string $name
 * @property integer $is_active
 * @property integer $account_invoice
 *
 * The followings are the available model relations:
 * @property ConstantText[] $constantTexts
 */
class ModuleType extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ModuleType the static model class
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
		return 'module_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, is_active, account_invoice', 'required'),
			array('is_active, account_invoice', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, is_active, account_invoice', 'safe', 'on'=>'search'),
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
			'constantTexts' => array(self::HAS_MANY, 'ConstantText', 'module_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'is_active' => 'Is Active',
			'account_invoice' => 'Account Invoice',
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
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('account_invoice',$this->account_invoice);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}