<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $username
 * @property string $loginname
 * @property string $tokenname
 * @property string $algorithm
 * @property string $salt
 * @property string $password
 * @property integer $is_first_password
 * @property integer $is_active
 * @property integer $is_super_admin
 * @property string $last_login
 * @property integer $failed_logins
 * @property string $session_id
 * @property integer $created_by
 * @property string $created_at
 * @property string $updated_at
 *
 * The followings are the available model relations:
 * @property Apart[] $aparts
 * @property Apart[] $aparts1
 * @property ApartCategory[] $apartCategories
 * @property ApartCategory[] $apartCategories1
 * @property ApartQuality[] $apartQualities
 * @property ApartQuality[] $apartQualities1
 * @property ApartSize[] $apartSizes
 * @property ApartSize[] $apartSizes1
 * @property Client[] $clients
 * @property Client[] $clients1
 * @property ClientRight[] $clientRights
 * @property ClientRight[] $clientRights1
 * @property ConstantText[] $constantTexts
 * @property ConstantText[] $constantTexts1
 * @property Country[] $countries
 * @property Country[] $countries1
 * @property County[] $counties
 * @property County[] $counties1
 * @property Credit[] $credits
 * @property Credit[] $credits1
 * @property Credit[] $credits2
 * @property CreditDeleteReason[] $creditDeleteReasons
 * @property CreditDeleteReason[] $creditDeleteReasons1
 * @property CreditPretense[] $creditPretenses
 * @property CreditPretense[] $creditPretenses1
 * @property Debit[] $debits
 * @property Debit[] $debits1
 * @property Debit[] $debits2
 * @property DebitDeleteReason[] $debitDeleteReasons
 * @property DebitDeleteReason[] $debitDeleteReasons1
 * @property DebitItem[] $debitItems
 * @property DebitItem[] $debitItems1
 * @property DebitItem[] $debitItems2
 * @property DebitPretenceItemPrice[] $debitPretenceItemPrices
 * @property DebitPretenceItemPrice[] $debitPretenceItemPrices1
 * @property DebitPretense[] $debitPretenses
 * @property DebitPretense[] $debitPretenses1
 * @property DebitPretenseItem[] $debitPretenseItems
 * @property DebitPretenseItem[] $debitPretenseItems1
 * @property Discount[] $discounts
 * @property Discount[] $discounts1
 * @property FileDescriptor[] $fileDescriptors
 * @property HolidayAllocationReason[] $holidayAllocationReasons
 * @property HolidayAllocationReason[] $holidayAllocationReasons1
 * @property HolidayChangeReason[] $holidayChangeReasons
 * @property HolidayChangeReason[] $holidayChangeReasons1
 * @property HolidayChangeRequest[] $holidayChangeRequests
 * @property HolidayChangeRequest[] $holidayChangeRequests1
 * @property HolidayChangeRequestDetail[] $holidayChangeRequestDetails
 * @property HolidayChangeRequestDetail[] $holidayChangeRequestDetails1
 * @property Hotel[] $hotels
 * @property Hotel[] $hotels1
 * @property Instr[] $instrs
 * @property Instr[] $instrs1
 * @property Instr[] $instrs2
 * @property InstrDelReason[] $instrDelReasons
 * @property InstrDelReason[] $instrDelReasons1
 * @property InstrDone[] $instrDones
 * @property InstrDone[] $instrDones1
 * @property InstrSource[] $instrSources
 * @property InstrSource[] $instrSources1
 * @property InstrTodo[] $instrTodos
 * @property InstrTodo[] $instrTodos1
 * @property Interest[] $interests
 * @property InterestSuspendLog[] $interestSuspendLogs
 * @property LedgerMirror[] $ledgerMirrors
 * @property LedgerMirror[] $ledgerMirrors1
 * @property MemberDataLog[] $memberDataLogs
 * @property MemberEntryReason[] $memberEntryReasons
 * @property MemberEntryReason[] $memberEntryReasons1
 * @property MemberLeaveReason[] $memberLeaveReasons
 * @property MemberLeaveReason[] $memberLeaveReasons1
 * @property MemberLevel[] $memberLevels
 * @property MemberLevel[] $memberLevels1
 * @property Permission[] $permissions
 * @property Permission[] $permissions1
 * @property Region[] $regions
 * @property Region[] $regions1
 * @property Right[] $rights
 * @property Right[] $rights1
 * @property RightBuyDelReason[] $rightBuyDelReasons
 * @property RightBuyDelReason[] $rightBuyDelReasons1
 * @property RightDenyExpectedIncome[] $rightDenyExpectedIncomes
 * @property RightDenyHolidayRotate[] $rightDenyHolidayRotates
 * @property RightDenyReason[] $rightDenyReasons
 * @property RightDenyReason[] $rightDenyReasons1
 * @property RightPause[] $rightPauses
 * @property RightPauseReason[] $rightPauseReasons
 * @property RightPauseReason[] $rightPauseReasons1
 * @property RightRelation[] $rightRelations
 * @property RightRelation[] $rightRelations1
 * @property RightType[] $rightTypes
 * @property RightType[] $rightTypes1
 * @property RotateLocRow[] $rotateLocRows
 * @property RotateLocRow[] $rotateLocRows1
 * @property RotateLocTable[] $rotateLocTables
 * @property RotateLocTable[] $rotateLocTables1
 * @property RotateTimeRowTurn[] $rotateTimeRowTurns
 * @property RotateTimeRowTurn[] $rotateTimeRowTurns1
 * @property RotateTimeTable[] $rotateTimeTables
 * @property RotateTimeTable[] $rotateTimeTables1
 * @property Settlement[] $settlements
 * @property Settlement[] $settlements1
 * @property Tax[] $taxes
 * @property Tax[] $taxes1
 * @property TimeGrid[] $timeGrs
 * @property TimeGrid[] $timeGrs1
 * @property TurnType[] $turnTypes
 * @property TurnType[] $turnTypes1
 * @property User $createdBy
 * @property User[] $users
 * @property UserGroup[] $userGroups
 * @property UserGroup[] $userGroups1
 * @property UserProfile[] $userProfiles
 * @property UserProfile[] $userProfiles1
 * @property UserProfile[] $userProfiles2
 * @property Voucher[] $vouchers
 * @property Voucher[] $vouchers1
 */
class User extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User the static model class
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
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, loginname, created_by, created_at', 'required'),
			array('is_first_password, is_active, is_super_admin, failed_logins, created_by', 'numerical', 'integerOnly'=>true),
			array('username, loginname, tokenname, algorithm, salt, password', 'length', 'max'=>128),
			array('session_id', 'length', 'max'=>64),
			array('last_login', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, username, loginname, tokenname, algorithm, salt, password, is_first_password, is_active, is_super_admin, last_login, failed_logins, session_id, created_by, created_at, updated_at', 'safe', 'on'=>'search'),
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
			'aparts' => array(self::HAS_MANY, 'Apart', 'created_by'),
			'aparts1' => array(self::HAS_MANY, 'Apart', 'updated_by'),
			'apartCategories' => array(self::HAS_MANY, 'ApartCategory', 'created_by'),
			'apartCategories1' => array(self::HAS_MANY, 'ApartCategory', 'updated_by'),
			'apartQualities' => array(self::HAS_MANY, 'ApartQuality', 'created_by'),
			'apartQualities1' => array(self::HAS_MANY, 'ApartQuality', 'updated_by'),
			'apartSizes' => array(self::HAS_MANY, 'ApartSize', 'created_by'),
			'apartSizes1' => array(self::HAS_MANY, 'ApartSize', 'updated_by'),
			'clients' => array(self::HAS_MANY, 'Client', 'created_by'),
			'clients1' => array(self::HAS_MANY, 'Client', 'updated_by'),
			'clientRights' => array(self::HAS_MANY, 'ClientRight', 'created_by'),
			'clientRights1' => array(self::HAS_MANY, 'ClientRight', 'updated_by'),
			'constantTexts' => array(self::HAS_MANY, 'ConstantText', 'created_by'),
			'constantTexts1' => array(self::HAS_MANY, 'ConstantText', 'updated_by'),
			'countries' => array(self::HAS_MANY, 'Country', 'created_by'),
			'countries1' => array(self::HAS_MANY, 'Country', 'updated_by'),
			'counties' => array(self::HAS_MANY, 'County', 'created_by'),
			'counties1' => array(self::HAS_MANY, 'County', 'updated_by'),
			'credits' => array(self::HAS_MANY, 'Credit', 'created_by'),
			'credits1' => array(self::HAS_MANY, 'Credit', 'updated_by'),
			'credits2' => array(self::HAS_MANY, 'Credit', 'deleted_by'),
			'creditDeleteReasons' => array(self::HAS_MANY, 'CreditDeleteReason', 'created_by'),
			'creditDeleteReasons1' => array(self::HAS_MANY, 'CreditDeleteReason', 'updated_by'),
			'creditPretenses' => array(self::HAS_MANY, 'CreditPretense', 'created_by'),
			'creditPretenses1' => array(self::HAS_MANY, 'CreditPretense', 'updated_by'),
			'debits' => array(self::HAS_MANY, 'Debit', 'created_by'),
			'debits1' => array(self::HAS_MANY, 'Debit', 'updated_by'),
			'debits2' => array(self::HAS_MANY, 'Debit', 'deleted_by'),
			'debitDeleteReasons' => array(self::HAS_MANY, 'DebitDeleteReason', 'created_by'),
			'debitDeleteReasons1' => array(self::HAS_MANY, 'DebitDeleteReason', 'updated_by'),
			'debitItems' => array(self::HAS_MANY, 'DebitItem', 'created_by'),
			'debitItems1' => array(self::HAS_MANY, 'DebitItem', 'updated_by'),
			'debitItems2' => array(self::HAS_MANY, 'DebitItem', 'deleted_by'),
			'debitPretenceItemPrices' => array(self::HAS_MANY, 'DebitPretenceItemPrice', 'created_by'),
			'debitPretenceItemPrices1' => array(self::HAS_MANY, 'DebitPretenceItemPrice', 'updated_by'),
			'debitPretenses' => array(self::HAS_MANY, 'DebitPretense', 'created_by'),
			'debitPretenses1' => array(self::HAS_MANY, 'DebitPretense', 'updated_by'),
			'debitPretenseItems' => array(self::HAS_MANY, 'DebitPretenseItem', 'created_by'),
			'debitPretenseItems1' => array(self::HAS_MANY, 'DebitPretenseItem', 'updated_by'),
			'discounts' => array(self::HAS_MANY, 'Discount', 'created_by'),
			'discounts1' => array(self::HAS_MANY, 'Discount', 'updated_by'),
			'fileDescriptors' => array(self::HAS_MANY, 'FileDescriptor', 'created_by'),
			'holidayAllocationReasons' => array(self::HAS_MANY, 'HolidayAllocationReason', 'created_by'),
			'holidayAllocationReasons1' => array(self::HAS_MANY, 'HolidayAllocationReason', 'updated_by'),
			'holidayChangeReasons' => array(self::HAS_MANY, 'HolidayChangeReason', 'created_by'),
			'holidayChangeReasons1' => array(self::HAS_MANY, 'HolidayChangeReason', 'updated_by'),
			'holidayChangeRequests' => array(self::HAS_MANY, 'HolidayChangeRequest', 'created_by'),
			'holidayChangeRequests1' => array(self::HAS_MANY, 'HolidayChangeRequest', 'updated_by'),
			'holidayChangeRequestDetails' => array(self::HAS_MANY, 'HolidayChangeRequestDetail', 'created_by'),
			'holidayChangeRequestDetails1' => array(self::HAS_MANY, 'HolidayChangeRequestDetail', 'updated_by'),
			'hotels' => array(self::HAS_MANY, 'Hotel', 'created_by'),
			'hotels1' => array(self::HAS_MANY, 'Hotel', 'updated_by'),
			'instrs' => array(self::HAS_MANY, 'Instr', 'resp_user_id'),
			'instrs1' => array(self::HAS_MANY, 'Instr', 'created_by'),
			'instrs2' => array(self::HAS_MANY, 'Instr', 'updated_by'),
			'instrDelReasons' => array(self::HAS_MANY, 'InstrDelReason', 'created_by'),
			'instrDelReasons1' => array(self::HAS_MANY, 'InstrDelReason', 'updated_by'),
			'instrDones' => array(self::HAS_MANY, 'InstrDone', 'created_by'),
			'instrDones1' => array(self::HAS_MANY, 'InstrDone', 'updated_by'),
			'instrSources' => array(self::HAS_MANY, 'InstrSource', 'created_by'),
			'instrSources1' => array(self::HAS_MANY, 'InstrSource', 'updated_by'),
			'instrTodos' => array(self::HAS_MANY, 'InstrTodo', 'created_by'),
			'instrTodos1' => array(self::HAS_MANY, 'InstrTodo', 'updated_by'),
			'interests' => array(self::HAS_MANY, 'Interest', 'created_by'),
			'interestSuspendLogs' => array(self::HAS_MANY, 'InterestSuspendLog', 'created_by'),
			'ledgerMirrors' => array(self::HAS_MANY, 'LedgerMirror', 'created_by'),
			'ledgerMirrors1' => array(self::HAS_MANY, 'LedgerMirror', 'updated_by'),
			'memberDataLogs' => array(self::HAS_MANY, 'MemberDataLog', 'created_by'),
			'memberEntryReasons' => array(self::HAS_MANY, 'MemberEntryReason', 'created_by'),
			'memberEntryReasons1' => array(self::HAS_MANY, 'MemberEntryReason', 'updated_by'),
			'memberLeaveReasons' => array(self::HAS_MANY, 'MemberLeaveReason', 'created_by'),
			'memberLeaveReasons1' => array(self::HAS_MANY, 'MemberLeaveReason', 'updated_by'),
			'memberLevels' => array(self::HAS_MANY, 'MemberLevel', 'created_by'),
			'memberLevels1' => array(self::HAS_MANY, 'MemberLevel', 'updated_by'),
			'permissions' => array(self::MANY_MANY, 'Permission', 'user_permission(user_id, permission_id)'),
			'permissions1' => array(self::HAS_MANY, 'Permission', 'updated_by'),
			'regions' => array(self::HAS_MANY, 'Region', 'created_by'),
			'regions1' => array(self::HAS_MANY, 'Region', 'updated_by'),
			'rights' => array(self::HAS_MANY, 'Right', 'created_by'),
			'rights1' => array(self::HAS_MANY, 'Right', 'updated_by'),
			'rightBuyDelReasons' => array(self::HAS_MANY, 'RightBuyDelReason', 'created_by'),
			'rightBuyDelReasons1' => array(self::HAS_MANY, 'RightBuyDelReason', 'updated_by'),
			'rightDenyExpectedIncomes' => array(self::HAS_MANY, 'RightDenyExpectedIncome', 'created_by'),
			'rightDenyHolidayRotates' => array(self::HAS_MANY, 'RightDenyHolidayRotate', 'created_by'),
			'rightDenyReasons' => array(self::HAS_MANY, 'RightDenyReason', 'created_by'),
			'rightDenyReasons1' => array(self::HAS_MANY, 'RightDenyReason', 'updated_by'),
			'rightPauses' => array(self::HAS_MANY, 'RightPause', 'created_by'),
			'rightPauseReasons' => array(self::HAS_MANY, 'RightPauseReason', 'created_by'),
			'rightPauseReasons1' => array(self::HAS_MANY, 'RightPauseReason', 'updated_by'),
			'rightRelations' => array(self::HAS_MANY, 'RightRelation', 'created_by'),
			'rightRelations1' => array(self::HAS_MANY, 'RightRelation', 'updated_by'),
			'rightTypes' => array(self::HAS_MANY, 'RightType', 'created_by'),
			'rightTypes1' => array(self::HAS_MANY, 'RightType', 'updated_by'),
			'rotateLocRows' => array(self::HAS_MANY, 'RotateLocRow', 'created_by'),
			'rotateLocRows1' => array(self::HAS_MANY, 'RotateLocRow', 'updated_by'),
			'rotateLocTables' => array(self::HAS_MANY, 'RotateLocTable', 'created_by'),
			'rotateLocTables1' => array(self::HAS_MANY, 'RotateLocTable', 'updated_by'),
			'rotateTimeRowTurns' => array(self::HAS_MANY, 'RotateTimeRowTurn', 'created_by'),
			'rotateTimeRowTurns1' => array(self::HAS_MANY, 'RotateTimeRowTurn', 'updated_by'),
			'rotateTimeTables' => array(self::HAS_MANY, 'RotateTimeTable', 'created_by'),
			'rotateTimeTables1' => array(self::HAS_MANY, 'RotateTimeTable', 'updated_by'),
			'settlements' => array(self::HAS_MANY, 'Settlement', 'created_by'),
			'settlements1' => array(self::HAS_MANY, 'Settlement', 'updated_by'),
			'taxes' => array(self::HAS_MANY, 'Tax', 'created_by'),
			'taxes1' => array(self::HAS_MANY, 'Tax', 'updated_by'),
			'timeGrs' => array(self::HAS_MANY, 'TimeGrid', 'created_by'),
			'timeGrs1' => array(self::HAS_MANY, 'TimeGrid', 'updated_by'),
			'turnTypes' => array(self::HAS_MANY, 'TurnType', 'created_by'),
			'turnTypes1' => array(self::HAS_MANY, 'TurnType', 'updated_by'),
			'createdBy' => array(self::BELONGS_TO, 'User', 'created_by'),
			'users' => array(self::HAS_MANY, 'User', 'created_by'),
			'userGroups' => array(self::MANY_MANY, 'UserGroup', 'user_user_group(user_id, user_group_id)'),
			'userGroups1' => array(self::HAS_MANY, 'UserGroup', 'updated_by'),
			'userProfile' => array(self::HAS_ONE, 'UserProfile', 'user_id'),
			'userProfiles1' => array(self::HAS_MANY, 'UserProfile', 'created_by'),
			'userProfiles2' => array(self::HAS_MANY, 'UserProfile', 'updated_by'),
			'vouchers' => array(self::HAS_MANY, 'Voucher', 'created_by'),
			'vouchers1' => array(self::HAS_MANY, 'Voucher', 'updated_by'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'username' => 'Felhasználó név',
			'loginname' => 'Bejelentkezési név',
			'tokenname' => 'Token név',
			'algorithm' => 'Algoritmus',
			'salt' => 'Só',
			'password' => 'Jelszó',
			'is_first_password' => 'Első jelszó',
			'is_active' => 'Aktív',
			'is_super_admin' => 'Super',
			'last_login' => 'Utolsó bejelentkezés',
			'failed_logins' => 'Sikertelen bejelentkezés',
			'session_id' => 'Session',
			'created_by' => 'Created By',
			'created_at' => 'Created At',
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
		$criteria->compare('username',$this->username,true);
		$criteria->compare('loginname',$this->loginname,true);
		$criteria->compare('tokenname',$this->tokenname,true);
		$criteria->compare('algorithm',$this->algorithm,true);
		$criteria->compare('salt',$this->salt,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('is_first_password',$this->is_first_password);
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('is_super_admin',$this->is_super_admin);
		$criteria->compare('last_login',$this->last_login,true);
		$criteria->compare('failed_logins',$this->failed_logins);
		$criteria->compare('session_id',$this->session_id,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('created_at',$this->created_at,true);
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