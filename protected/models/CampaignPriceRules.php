<?php

/**
 * This is the model class for table "campaign_price_rules".
 *
 * The followings are the available columns in table 'campaign_price_rules':
 * @property integer $id
 * @property integer $campaign_id
 * @property integer $lft
 * @property integer $rgt
 * @property integer $level
 * @property string $name
 * @property integer $price
 * @property integer $percent
 * @property integer $link_id
 * @property string $link_type
 * @property string $price_type
 */
class CampaignPriceRules extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CampaignPriceRules the static model class
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
		return 'campaign_price_rules';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('campaign_id, lft, rgt, level', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('link_type', 'length', 'max'=>128),
			array('price_type', 'length', 'max'=>7),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, campaign_id, lft, rgt, level, name, price, percent, link_id, link_type, price_type', 'safe', 'on'=>'search'),
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
			'lft' => 'Lft',
			'rgt' => 'Rgt',
			'level' => 'Level',
			'name' => 'Name',
			'price' => 'Price',
			'percent' => 'Percent',
			'link_id' => 'Link',
			'link_type' => 'Link Type',
			'price_type' => 'Price Type',
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
		$criteria->compare('lft',$this->lft);
		$criteria->compare('rgt',$this->rgt);
		$criteria->compare('level',$this->level);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('price',$this->price);
		$criteria->compare('percent',$this->percent);
		$criteria->compare('link_id',$this->link_id);
		$criteria->compare('link_type',$this->link_type,true);
		$criteria->compare('price_type',$this->price_type,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function behaviors()
	{
	    return array(
	        'NestedSetBehavior'=>array(
	            'class'=>'ext.behaviors.ENestedSetBehavior',
	            'leftAttribute'=>'lft',
	            'rightAttribute'=>'rgt',
	            'levelAttribute'=>'level',
	            'rootAttribute' => 'campaign_id',
	            'hasManyRoots' => true,
	         )
	    );
	}
}