<?php
class MemberManager extends BaseModelManager
{
    private static $instance = null;

    private function __construct() {

    }

    public static function getSex(){
        return array(
            0 => 'Úr',
            1 => 'Úrhölgy',
        );
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new MemberManager();
        }
        return self::$instance;
    }

    /*public function getMembers($extra_params = array())
    {
        $query_params = array(
            array('select', 'c.firm, ms.name AS member_status, c.id, c.name, c.sex,c.identifier, c.old_identifier, CONCAT(c.zip, " ", s.name, ", ", c.street) as address, c.notify_name, CONCAT(c.notify_zip, " ", n_s.name, ", ", c.notify_street) as notify_address, c.notify_phone, c.notify_email'),
            array('from', 'client c'),
            array('join', array('settlement s', 'c.settlement_id = s.id')),
            array('join', array('settlement n_s', 'c.notify_settlement_id = n_s.id')),
            array('join', array('member_status ms', 'ms.id = c.member_status_id')),
        );

        $query_params = array_merge($query_params, $extra_params);

        return DBManager::getInstance()->query($query_params);
    }*/
    
    public function getMembers($extra_params = array())
    {
    	$query_params = array(
    			array('select', 'id, name, email, birthdate, address, sex'),
    			array('from', 'member'),
    	);
    
    	$query_params = array_merge($query_params, $extra_params);
    
    	return DBManager::getInstance()->query($query_params);
    }

	public function getMemberCampaignTypes($memberId, $extra_params = array())
    {
    	$query_params = array(
			array('select', 'mct.id,dt.name AS dance_type_name, ct.name AS campaign_type_name, mct.done, mct.start_date, mct.end_date'),
			array('from', 'member_campaign_type mct'),
			array('join', array(CampaignType::model()->tableName().' ct','mct.campaign_type_id=ct.id')),
			array('join', array(DanceType::model()->tableName().' dt','ct.dance_type_id=dt.id')),
			array('where', array("mct.member_id=:mid", array(':mid' => $memberId))),
    	);
    
    	$query_params = array_merge($query_params, $extra_params);
    
    	return DBManager::getInstance()->query($query_params);
    }
/*
    public function getMemberHistory($client_id, $extra_params = array())
    {
        $results = array();
        $client = new Client();
        $labels = $client->attributeLabels();

        $query_params = array(
            array('select', 'mdl.id, mdl.attr_name, mdl.old_value, mdl.new_value, u.username, mdl.created_at'),
            array('from', MemberDataLog::model()->tableName().' mdl'),
            array('join', array(User::model()->tableName().' u','mdl.created_by=u.id')),
        );
        // elvileg az extra_paramsban is érkezhetnek where-re vonatkozó kikötések ezért merge-elni kell query where-jével
        $where_params = array('str' =>'mdl.client_id = :client_id', 'params' =>array(':client_id' => $client_id));
        $where_params = $this->mergeWhereAndExtraParams($where_params, $extra_params);

        $query_params[] = array('where', array($where_params['str'], $where_params['params']));

        $query_params = array_merge($query_params, $extra_params);

        $results = DBManager::getInstance()->query($query_params);

        foreach($results['data'] AS &$data){
            $data['attr_name'] = $labels[$data['attr_name']];
        }

        return $results;
    }

    public function customerServiceIsOK($client_id){
        return (Instr::model()->with('instrStatus')->count('client_id=:client_id AND (reported=1 OR working=1)', array(':client_id' => $client_id))>0)?false:true;
    }

    public function financeOk($client_id){
        $account = Account::model()->find('member_id=:member_id', array(':member_id' => $client_id));

        if($account->balance==0 && $account->undebited_interest==0){
            return true;
        }
        return false;
    }

    public function makeAccount($member_id){
        $account = Account::model()->find('member_id=:member_id', array(':member_id' => $member_id));

        if(is_null($account)){
            $acc = new Account();
            $acc->member_id = $member_id;
            $acc->balance = 0;
            $acc->undebited_interest = 0;
            $acc->save();
        }
    }
*/
    public function save($params) {
        if ($params['id']) {
            $record = Member::model()->findByPk($params['id']);
        } else {
            $record = new Member();
        }

        $form = new MemberForm();

        $form->bindActiveRecord($record);
        $form->bind($params);

        if ($form->validate()) {
            if($form->save()){
                
            }
        }

        $errors = $form->getErrors();
        if (empty($errors)) {
            $response = array(
                        'success'=>true,
                        'message'=>'Az adatok sikeresen rögzítve.',
            			'id' => $form->id,
            );
        } else {
            $response = array(
                        'success'=>false,
                        'message'=>'Az adatok módosítása nem sikerült az alábbi hibák miatt:',
                        'errors'=>Arrays::array_flatten($errors),
            );
        }

        return $response;
    }
/*
    public function setStatus($client_id, $status_column){
        $id = StatusManager::getInstance()->getId('MemberStatus', $status_column);

        $client = Client::model()->findByPk($client_id);
        $client->member_status_id = $id;
       return $client->save();
    }

    public function generateIdentifier(){
        $id = new MemberIdentifier();
        $id->save();

        return $id->id;
    }

    public function setRightStatus($client_id, $status_column){
        $status = RightStatus::model()->find('`'.$status_column.'`=1');

        Right::model()->updateAll(array('status_id' => $status->id, 'active_to' => sfDate::getInstance()->formatDbDate()), 'member_id=:member_id AND del_reason_id IS NOT NULL AND active_to IS NOT NULL', array(':member_id' => $client_id));
    }

    
    public function setRightsLogout($client_id, $active_to){
        Yii::import('application.modules.right.components.RightManager');
        $rm = RightManager::getInstance();
        $last_day = sfDate::getInstance(date('Y').'-12-31')->formatDbDate();

        $rights = Right::model()->findAll('member_id=:client_id AND del_reason_id IS NULL', array(':client_id' => $client_id));
        $logout_status = $this->getRightLogoutStatus();
        $active_to = sfDate::getInstance($active_to)->formatDbDate();
        $del_status = StatusManager::getInstance()->getId('RightStatus','deleted');

        foreach($rights AS $right){
            $right->del_reason_id = $logout_status;
            $right->active_to = $active_to;
            $right->status_id = $del_status;

            if($right->save()){
                Allocation::model()->deleteAll('`right_id`=:right_id AND `from`>:active_to', array(':right_id' => $right->id, ':active_to' => $last_day));
                $rm->holidayDelete($client_id, 'client_id', $last_day);
            }
        }
    }

    private function getRightLogoutStatus(){
        return StatusManager::getInstance()->getId('RightBuyDelReason','logout');
    }*/
}

?>