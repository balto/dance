<?php

/**
 * This is the model class for table "client".
 *
 * The followings are the available columns in table 'client':
 * @property integer $id
 * @property string $name
 * @property integer $sex
 * @property integer $identifier
 * @property string $old_identifier
 * @property integer $firm
 * @property string $firm_repr_name
 * @property string $firm_reg_num
 * @property string $mother_name
 * @property integer $birth_place_id
 * @property string $birth_date
 * @property string $id_card_num
 * @property string $tax_number
 * @property string $bank_account_num
 * @property integer $settlement_id
 * @property string $street
 * @property string $zip
 * @property string $phone
 * @property string $fax
 * @property string $email
 * @property string $notify_name
 * @property integer $notify_sex
 * @property string $notify_firm_admin_name
 * @property integer $notify_settlement_id
 * @property string $notify_street
 * @property string $notify_zip
 * @property string $notify_phone
 * @property string $notify_fax
 * @property string $notify_email
 * @property integer $member
 * @property integer $died
 * @property integer $interest_suspended
 * @property integer $member_status_id
 * @property integer $member_level_id
 * @property integer $member_entry_reason_id
 * @property string $member_entry_at
 * @property integer $member_leave_reason_id
 * @property string $member_leave_at
 * @property integer $member_can_vote
 * @property integer $member_card_printed
 * @property string $comment
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * The followings are the available model relations:
 * @property Account[] $accounts
 * @property Settlement $birthPlace
 * @property Settlement $settlement
 * @property Settlement $notifySettlement
 * @property MemberStatus $memberStatus
 * @property MemberLevel $memberLevel
 * @property MemberEntryReason $memberEntryReason
 * @property MemberLeaveReason $memberLeaveReason
 * @property User $createdBy
 * @property User $updatedBy
 * @property ClientRight[] $clientRights
 * @property Holiday[] $holidays
 * @property HolidayVersion[] $holidayVersions
 * @property Instr[] $instrs
 * @property MemberDataLog[] $memberDataLogs
 * @property Right[] $rights
 * @property Right[] $rights1
 */
class Client extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Client the static model class
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
		return 'client';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, firm, settlement_id, street, zip, notify_name, notify_sex, notify_settlement_id, notify_street, notify_zip, member, died, interest_suspended, member_status_id, created_by, created_at', 'required'),
			array('sex, identifier, firm, birth_place_id, settlement_id, notify_sex, notify_settlement_id, member, died, interest_suspended, member_status_id, member_level_id, member_entry_reason_id, member_leave_reason_id, member_can_vote, member_card_printed, created_by, updated_by', 'numerical', 'integerOnly'=>true),
			array('name, firm_repr_name, firm_reg_num, mother_name, tax_number, street, phone, fax, email, notify_name, notify_firm_admin_name, notify_street, notify_phone, notify_fax, notify_email', 'length', 'max'=>255),
			array('old_identifier, id_card_num', 'length', 'max'=>50),
			array('bank_account_num', 'length', 'max'=>100),
			array('zip, notify_zip', 'length', 'max'=>10),
			array('birth_date, member_entry_at, member_leave_at, comment, updated_at', 'safe'),
            array('birth_date, member_entry_at, member_leave_at', 'date', 'format'=>Yii::app()->params['yii_date_format']),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, sex, identifier, old_identifier, firm, firm_repr_name, firm_reg_num, mother_name, birth_place_id, birth_date, id_card_num, tax_number, bank_account_num, settlement_id, street, zip, phone, fax, email, notify_name, notify_sex, notify_firm_admin_name, notify_settlement_id, notify_street, notify_zip, notify_phone, notify_fax, notify_email, member, died, interest_suspended, member_status_id, member_level_id, member_entry_reason_id, member_entry_at, member_leave_reason_id, member_leave_at, member_can_vote, member_card_printed, comment, created_by, created_at, updated_by, updated_at', 'safe', 'on'=>'search'),
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
			'account' => array(self::HAS_ONE, 'Account', 'member_id'),
			'birthPlace' => array(self::BELONGS_TO, 'Settlement', 'birth_place_id'),
			'settlement' => array(self::BELONGS_TO, 'Settlement', 'settlement_id'),
			'notifySettlement' => array(self::BELONGS_TO, 'Settlement', 'notify_settlement_id'),
			'memberStatus' => array(self::BELONGS_TO, 'MemberStatus', 'member_status_id'),
			'memberLevel' => array(self::BELONGS_TO, 'MemberLevel', 'member_level_id'),
			'memberEntryReason' => array(self::BELONGS_TO, 'MemberEntryReason', 'member_entry_reason_id'),
			'memberLeaveReason' => array(self::BELONGS_TO, 'MemberLeaveReason', 'member_leave_reason_id'),
			'createdBy' => array(self::BELONGS_TO, 'User', 'created_by'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
			'clientRights' => array(self::HAS_MANY, 'ClientRight', 'client_id'),
			'holidays' => array(self::HAS_MANY, 'Holiday', 'client_id'),
			'holidayVersions' => array(self::HAS_MANY, 'HolidayVersion', 'client_id'),
			'instrs' => array(self::HAS_MANY, 'Instr', 'client_id'),
			'memberDataLogs' => array(self::HAS_MANY, 'MemberDataLog', 'client_id'),
			'rights' => array(self::HAS_MANY, 'Right', 'member_id'),
			'rights1' => array(self::HAS_MANY, 'Right', 'notif_client_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Törzsszám',
			'name' => 'Név',
			'sex' => 'Megszólítás',
            'identifier' => 'Törzsszám',
			'old_identifier' => 'Régi törzsszám',
			'firm' => 'Cég',
			'firm_repr_name' => 'Képviselő',
			'firm_reg_num' => 'Cégbejegyz. sz.',
			'mother_name' => 'Anyja neve',
			'birth_place_id' => 'Születési hely',
			'birth_date' => 'Született',
			'id_card_num' => 'Személyi ig. szám',
			'tax_number' => 'Adószám',
			'bank_account_num' => 'Bankszámlaszám',
			'settlement_id' => 'Lakcím',
			'street' => 'Utca, házszám',
			'zip' => 'Irányítószám',
			'phone' => 'Telefon',
			'fax' => 'Fax',
			'email' => 'E-mail',
			'notify_name' => 'Értesítési név',
			'notify_sex' => 'Értesítendő megszólítása',
			'notify_firm_admin_name' => 'Ügyintéző',
			'notify_settlement_id' => 'Értesítési cím',
			'notify_street' => 'Értesítési utca, házszám',
			'notify_zip' => 'Értesítési irányítószám',
			'notify_phone' => 'Értesítési telefon',
			'notify_fax' => 'Értesítési fax',
			'notify_email' => 'Értesítési e-mail',
			'member' => 'Tag',
			'died' => 'Elhunyt',
			'interest_suspended' => 'Kamatszámítás felfüggesztése',
			'member_status_id' => 'Állapot',
			'member_level_id' => 'Szint',
			'member_entry_reason_id' => 'Jogcím',
			'member_entry_at' => 'Belépés',
			'member_leave_reason_id' => 'Jogcím',
			'member_leave_at' => 'Kilépés',
			'member_can_vote' => 'Szavazhat',
			'member_card_printed' => 'Member Card Printed',
			'comment' => 'Megjegyzés',
			'created_by' => 'Felvitte',
			'created_at' => 'Felvitel dátuma',
			'updated_by' => 'Módosította',
			'updated_at' => 'Utolsó módosítás',
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
		$criteria->compare('sex',$this->sex);
		$criteria->compare('identifier',$this->identifier);
		$criteria->compare('old_identifier',$this->old_identifier,true);
		$criteria->compare('firm',$this->firm);
		$criteria->compare('firm_repr_name',$this->firm_repr_name,true);
		$criteria->compare('firm_reg_num',$this->firm_reg_num,true);
		$criteria->compare('mother_name',$this->mother_name,true);
		$criteria->compare('birth_place_id',$this->birth_place_id);
		$criteria->compare('birth_date',$this->birth_date,true);
		$criteria->compare('id_card_num',$this->id_card_num,true);
		$criteria->compare('tax_number',$this->tax_number,true);
		$criteria->compare('bank_account_num',$this->bank_account_num,true);
		$criteria->compare('settlement_id',$this->settlement_id);
		$criteria->compare('street',$this->street,true);
		$criteria->compare('zip',$this->zip,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('fax',$this->fax,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('notify_name',$this->notify_name,true);
		$criteria->compare('notify_sex',$this->notify_sex);
		$criteria->compare('notify_firm_admin_name',$this->notify_firm_admin_name,true);
		$criteria->compare('notify_settlement_id',$this->notify_settlement_id);
		$criteria->compare('notify_street',$this->notify_street,true);
		$criteria->compare('notify_zip',$this->notify_zip,true);
		$criteria->compare('notify_phone',$this->notify_phone,true);
		$criteria->compare('notify_fax',$this->notify_fax,true);
		$criteria->compare('notify_email',$this->notify_email,true);
		$criteria->compare('member',$this->member);
		$criteria->compare('died',$this->died);
		$criteria->compare('interest_suspended',$this->interest_suspended);
		$criteria->compare('member_status_id',$this->member_status_id);
		$criteria->compare('member_level_id',$this->member_level_id);
		$criteria->compare('member_entry_reason_id',$this->member_entry_reason_id);
		$criteria->compare('member_entry_at',$this->member_entry_at,true);
		$criteria->compare('member_leave_reason_id',$this->member_leave_reason_id);
		$criteria->compare('member_leave_at',$this->member_leave_at,true);
		$criteria->compare('member_can_vote',$this->member_can_vote);
		$criteria->compare('member_card_printed',$this->member_card_printed);
		$criteria->compare('comment',$this->comment,true);
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
            'Log' => array(
                'class' => 'ext.behaviors.LogBehavior',
                'log_model_name' => 'MemberDataLog',
                'logging_columns' => array(
                    'name',
                    'sex',
                    'identifier',
                    'old_identifier',
                    'firm',
                    'firm_repr_name',
                    'firm_reg_num',
                    'mother_name',
                    'birth_place_id',
                    'birth_date',
                    'id_card_num',
                    'tax_number',
					'bank_account_num',
                    'settlement_id',
                    'street',
                    'zip',
                    'phone',
                    'fax',
                    'email',
                    'notify_name',
                    'notify_sex',
                    'notify_firm_admin_name',
                    'notify_settlement_id',
                    'notify_street',
                    'notify_zip',
                    'notify_phone',
                    'notify_fax',
                    'notify_email',
                    'member',
                    'died',
                    'interest_suspended',
                    'member_status_id',
                    'member_level_id',
                    'member_entry_reason_id',
                    'member_entry_at',
                    'member_leave_reason_id',
                    'member_leave_at',
                    'member_can_vote',
                    'member_card_printed',
                    'comment',
                ),
                'foreign_columns' => array(
                    'sex' => array('class' => 'MemberManager', 'function' => 'getSex'),
                    'notify_sex' => array('class' => 'MemberManager', 'function' => 'getSex'),
                    'birth_place_id' => array('model' => 'Settlement', 'column' => 'name'),
                    'settlement_id' => array('model' => 'Settlement', 'column' => 'name'),
                    'notify_settlement_id' => array('model' => 'Settlement', 'column' => 'name'),
                    'member_status_id' => array('model' => 'MemberStatus', 'column' => 'name'),
                    'member_level_id' => array('model' => 'MemberLevel', 'column' => 'name'),
                    'member_entry_reason_id' => array('model' => 'MemberEntryReason', 'column' => 'name'),
                    'member_leave_reason_id' => array('model' => 'MemberLeaveReason', 'column' => 'name'),
                ),
                'required' => array('member_leave_at', 'member_entry_at'),
                'link_id_col_name' => "client_id",
            ),
        );
    }
}