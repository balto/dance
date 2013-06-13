<?php
class CampaignManager extends BaseModelManager
{
    private static $instance = null;

    private function __construct() {
		
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new CampaignManager();
        }
        return self::$instance;
    }

	/**
     * Torzsadatok / Kampany tipusok - kampany tipusok lista
     * 
     * @param unknown_type $extra_params
     * @param unknown_type $isCombo
     */
    public function getCampaignTypes($extra_params = array(), $isCombo = false, $isCtPermissionShow = false)
    {
    	$selectArray = array(
    		'ct.id',
    		'ct.name',
    		'dt.name' => 'dance_type_name',
    		'COALESCE(GROUP_CONCAT(CONCAT(dt2.name," ",ct2.name) SEPARATOR ", "), "...nincs kötelező kampány típus") as required_campaign_types',
			'COALESCE(GROUP_CONCAT(CONCAT(ctd.moment_count,"/",ctd.required_moment_count) SEPARATOR ", "), "...nincs felvéve alkalom") as campaign_type_moments',	
		);
    	
    	if ($isCtPermissionShow) {
    		$selectArray[] = '"1" AS is_free';
    	}
    	
    	if ($isCombo) {
    		$selectArray = array(	
    			'CONCAT(dt.name," ",ct.name)' => 'name',
    		);
    		
    		$select = $this->getSelectColumnsForCombo($selectArray, "ct.id");
    	}
    	else{
    		$select = $this->getSelectColumnsForGrid($selectArray);
    	}
    	
        $query_params = array(
            array('select', $select),
            array('from', 'campaign_type ct'),
            array('join', array('dance_type dt', 'ct.dance_type_id = dt.id')),
            array('join', array('campaign_type_detail ctd', 'ctd.campaign_type_id = ct.id')),
            array('leftJoin', array('campaign_type_require ctr', 'ctr.campaign_type_id = ct.id')),
            array('leftJoin', array('campaign_type ct2', 'ct2.id = ctr.require_campaign_type_id')),
            array('leftJoin', array('dance_type dt2', 'dt2.id = ct2.dance_type_id')),
            array('group', 'ct.id'),
        );

        $query_params = array_merge($query_params, $extra_params);

        $results = DBManager::getInstance()->query($query_params);
        
        foreach ($results['data'] AS &$data){
        	$reqctExpls = array();
        	$reqctExpls = explode(', ', $data['required_campaign_types']);
        	$reqctTemp = array();
        	
        	foreach ($reqctExpls as $reqct) {
        		$reqctTemp[$reqct] = null;
        	}
        	
        	$ctmExpls = array();
        	$ctmExpls = explode(', ', $data['campaign_type_moments']);
        	$ctmTemp = array();
        	 
        	foreach ($ctmExpls as $ctm) {
        		$ctmTemp[$ctm] = null;
        	}
        	
        	$data['required_campaign_types'] = implode(', ', array_keys($reqctTemp));
        	$data['campaign_type_moments'] = implode(', ', array_keys($ctmTemp));
        }
        
        return $results;
    }

    /**
     * Torzsadatok / Kampany tipusok - kampany tipusok lista
     * 
     * @param unknown_type $extra_params
     * @param unknown_type $isCombo
     */
    public function getCampaignTypeDetailList($extra_params = array(), $isCombo = false)
    {
    	$selectArray = array(
    		'ctd.id',
    		'ct.name',
    		'dt.name' => 'dance_type_name',
    		'moment_count',
    		'required_moment_count',
    	);
    	
    	if ($isCombo) {
    		$selectArray = array(	
    			'CONCAT(dt.name," ",ct.name," ",ctd.moment_count," alkalmas")' => 'name',
    		);
    		
    		$select = $this->getSelectColumnsForCombo($selectArray, "ctd.id");
    	}
    	else{
    		$select = $this->getSelectColumnsForGrid($selectArray);
    	}
    	
        $query_params = array(
            array('select', $select),
            array('from', CampaignTypeDetail::model()->tableName().' ctd'),
            array('join', array('campaign_type ct', 'ctd.campaign_type_id = ct.id')),
            array('join', array('dance_type dt', 'ct.dance_type_id = dt.id')),
        );

        $query_params = array_merge($query_params, $extra_params);

        return DBManager::getInstance()->query($query_params);
    }
    
    
	/**
	 * Torzsadatok / Kampany tipusok - kampany tipus szerkesztese ablakban levo kotelezo kampany tipus gridnek a listaja
	 * 
	 * @param unknown_type $campaignTypeId
	 * @param unknown_type $extra_params
	 */
    public function getRequiredCampaignTypes($campaignTypeId = 0, $extra_params = array())
    {	
    	$query_params = array(
    			//array('select', 'ct.id, ct.name, dt.name AS dance_type_name, moment_count, required_moment_count, ctr.group AS campaign_type_group'),
    			array('select', 'ct.id, ct.name, dt.name AS dance_type_name, ctr.group AS campaign_type_group'),
    			array('from', CampaignTypeRequire::model()->tableName().' ctr'),
    			array('join', array(CampaignType::model()->tableName().' ct', 'ctr.require_campaign_type_id = ct.id')),
    			array('join', array('dance_type dt', 'ct.dance_type_id = dt.id')),
    			array('where', array('ctr.campaign_type_id=:campaign_type_id', array(':campaign_type_id' => $campaignTypeId))),
    			/* array('join', array('settlement n_s', 'c.notify_settlement_id = n_s.id')),
    			 array('join', array('member_status ms', 'ms.id = c.member_status_id')),*/
    	);
    
    	$query_params = array_merge($query_params, $extra_params);
    
    	return DBManager::getInstance()->query($query_params);
    }
	
	/**
	 * Torzsadatok / Kampany tipusok - kampany tipus szerkesztese ablakban levo kotelezo kampany tipus gridnek a listaja
	 * 
	 * @param unknown_type $campaignTypeId
	 * @param unknown_type $extra_params
	 */
    public function getCampaignTypeDetails($campaignTypeId = 0, $extra_params = array())
    {	
    	$query_params = array(
    			//array('select', 'ct.id, ct.name, dt.name AS dance_type_name, moment_count, required_moment_count, ctr.group AS campaign_type_group'),
    			array('select', 'id, moment_count, required_moment_count, required_moments'),
    			array('from', CampaignTypeDetail::model()->tableName()),
    			array('where', array('campaign_type_id=:campaign_type_id', array(':campaign_type_id' => $campaignTypeId))),
    			/* array('join', array('settlement n_s', 'c.notify_settlement_id = n_s.id')),
    			 array('join', array('member_status ms', 'ms.id = c.member_status_id')),*/
    	);
    
    	$query_params = array_merge($query_params, $extra_params);
    
    	return DBManager::getInstance()->query($query_params);
    }
	
	
	
	/**
	 * Torzsadatok / Kampany tipusok - kampany tipus szerkesztese ablakban levo jogosult kampany tipus gridnek a listaja
	 * 
	 * @param unknown_type $campaignTypeId
	 * @param unknown_type $extra_params
	 */
    /*public function getPermissionedCampaignTypes($campaignTypeId = 0, $extra_params = array())
    {
    	$query_params = array(
    			array('select', 'ct.id, ct.name, dt.name AS dance_type_name, ctp.is_free'),
    			array('from', CampaignTypePermission::model()->tableName().' ctp'),
    			array('join', array(CampaignType::model()->tableName().' ct', 'ctp.permission_campaign_type_id = ct.id')),
    			array('join', array('dance_type dt', 'ct.dance_type_id = dt.id')),
    			array('where', array('ctp.campaign_type_id=:campaign_type_id', array(':campaign_type_id' => $campaignTypeId))),
    	);
    
    	$query_params = array_merge($query_params, $extra_params);
    
    	return DBManager::getInstance()->query($query_params);
    }*/
    
    /**
     * Torzsadatok / Kampany tipusok - lekeri a kampany tipushoz tartozo kampany tipus alkalmakat
     * 
     * @param unknown_type $campaignTypeId
     * @return multitype:multitype:unknown
     */
    public function getCampaignTypeDetailMoments($campaignTypeDetailId){
    	$query_params = array(
    		array('select', 'id, moment_number, is_required'),
    		array('from', CampaignTypeDetailMoment::model()->tableName().' ctdm'),
    		array('where', array('ctdm.campaign_type_detail_id=:campaign_type_detail_id', array(':campaign_type_detail_id' => $campaignTypeDetailId))),
    	);
    	
    	$res = DBManager::getInstance()->query($query_params);
    	
    	$result = array();
    	
    	foreach ($res['data'] as $data){
    		$result[$data['moment_number']] = array('id' => $data['id'], 'is_required' => $data['is_required']);
    	}
    	
    	return $result;
    }
    
    /**
     * stringbol csinal tombot
     * pl. : 1,2,4-6
     * 
     * array(1,2,4,5,6)
     * @param unknown_type $requiredMomentsString
     */
    private function getRequiredMomentsArray($requiredMomentCount, $requiredMomentsString){
    	$reqM = explode(',', $requiredMomentsString);
    	$requiredMoments = array();
    	
    	foreach ($reqM as $rm) {
    		$rmSlice = explode('-', $rm);
    		 
    		if(count($rmSlice)==2){
    			$start = $rmSlice[0];
    			$end   = $rmSlice[1];
    				
    			$rmSlice = array();
    				
    			for ($i = $start; $i <= $end; $i++){
    				$rmSlice[] = $i;
    			}
    		}
    		 
    		foreach ($rmSlice as $num){
    			if($num>=1 && count($requiredMoments)<=$requiredMomentCount){
    				$requiredMoments[] = $num;
    			}
    		}
    	}
    		
    	return array_unique($requiredMoments);
    }
    /**
     * Torzsadatok / Kampany tipusok - kampany tipus mentese
     * 
     * @param unknown_type $model_name
     * @param unknown_type $params
     * @param unknown_type $form_name
     * @param unknown_type $requiredCampaignTypes
     * @return Ambigous <multitype:boolean string NULL , multitype:boolean string Ambigous <multitype:, multitype:multitype:string unknown  > >
     */
    public function saveCampaignType($model_name, $params, $form_name, $requiredCampaignTypes = array(), $campaignTypeDetails = array()) {
    	$response = parent::save($model_name, $params, $form_name);

    	if($response['success']){
    		foreach ($campaignTypeDetails as $md) {
    			$ctd_temp = explode(',', $md);

    			$ctd_id = trim($ctd_temp[0]);
				$ctd_moment_count = trim($ctd_temp[1]);
				$ctd_required_moment_count = trim($ctd_temp[2]);
				$ctd_required_moments = trim($ctd_temp[3]);

    			if(empty($ctd_id)){
    				$ctd = new CampaignTypeDetail();
					$ctd->campaign_type_id = $response['id'];
					$ctd->moment_count = $ctd_moment_count;
					$ctd->required_moment_count = $ctd_required_moment_count;
					$ctd->required_moments = $ctd_required_moments;
					
					if($ctd->save()){
						$requiredMoments = array();
					
						if(!empty($ctd_moment_count)){
			    			if(isset($ctd_required_moments) && $ctd_required_moments!=''){
			    				 
			    				$requiredMoments = $this->getRequiredMomentsArray($ctd_moment_count, $ctd_required_moments);
			    			 
			    			}
			    			
			    			for($i = 1; $i <= $ctd_moment_count; $i++){
			    				$required = (in_array($i, $requiredMoments)) ? 1 : 0;
	
		    					$ctm = new CampaignTypeDetailMoment();
		    					$ctm->campaign_type_detail_id = $ctd->id;
		    					$ctm->moment_number = $i;
		    					$ctm->is_required = $required;
		    					$ctm->save();
			    			}
			    		}
			    		elseif (empty($ctd_moment_count)){//ha nem toltotte ki a moment_countot, tehat folyamatos a kurzus, akkor a moment tablaba 1 sor lesz
			    			$ctm = new CampaignTypeDetailMoment();
			    			$ctm->campaign_type_detail_id = $ctd->id;
			    			$ctm->moment_number = 1;
			    			$ctm->is_required = 0;
			    			$ctm->save();
			    		}
					}
				}
			}

    		//kötelező meglévő tanfolyamok
    		if(isset($params['id']) && !empty($params['id'])){
				CampaignTypeRequire::model()->deleteAll("campaign_type_id =:campaign_type_id", array(':campaign_type_id' => $params['id']));
			}

			foreach ($requiredCampaignTypes AS $ctIdGroup){
				$helper = explode(',', $ctIdGroup);
				
				$ctr = new CampaignTypeRequire();
				$ctr->campaign_type_id = $response['id'];
				$ctr->require_campaign_type_id = $helper[0];
				$ctr->group = $helper[1];
				$ctr->save();
			}
    	}
    	
    	return $response;
    }
    
    /**
     * Meglevore allitja a megadott membernek a megadott kampany tipusokat
     * 
     * @param unknown_type $memberId
     * @param array $campaignTypesIds
     */
    public function saveCampaignTypesForMember($memberId, array $campaignTypesIds){
    	//$allRows = $this->getAllCampaignTypeToMember($memberId);
    	
    	//if(!$allRows['totalCount']){
    		foreach ($campaignTypesIds as $ctid) {
    			$mct = new MemberCampaignType();
    			$mct->campaign_type_id = $ctid;
    			$mct->member_id = $memberId;
    			$mct->done = 1;
    			$mct->start_date = sfDate::getInstance()->formatDbDateTime();
    			$mct->end_date = sfDate::getInstance()->formatDbDateTime();
    			$mct->save();
    		}
    	//}
    	
    }
    
    //jelenléti ív
    
    
    /**
     * Kampany box - kampany lista
     * 
     * @param unknown_type $extra_params
     */
    public function getCampaigns($extra_params = array(), $campaignId = null, $onlyActive = 0)
    {
    	$end_of_day = date("Y-m-d")." 23:59:59";
		
    	$query_params = array(
    		array('select', "c.id,ct.id AS campaign_type_id, COUNT(cm.id) AS campaign_moment_count, ctd.moment_count AS campaign_type_detail_moment_count, ct.name AS campaign_type_name, dt.name AS dance_type_name, l.name AS location_name, LEFT(c.start_datetime,16) AS start_date, end_datetime, IF(end_datetime IS NULL OR end_datetime > '".$end_of_day."', 0, 1) AS completed"),
    		array('from', Campaign::model()->tableName().' c'),
    		array('join', array(CampaignTypeDetail::model()->tableName() . ' ctd', 'c.campaign_type_detail_id = ctd.id')),
    		array('join', array(CampaignType::model()->tableName() . ' ct', 'ctd.campaign_type_id = ct.id')),
    		array('join', array(Location::model()->tableName() . ' l', 'c.location_id = l.id')),
    		array('join', array(DanceType::model()->tableName() . ' dt', 'ct.dance_type_id = dt.id')),
    		array('leftJoin', array(CampaignMoment::model()->tableName().' cm', 'cm.campaign_id = c.id')),
    		
    	);
		
		if(!is_null($campaignId)){
			$query_params[] = array('where', array('c.id=:campaign_id', array(':campaign_id' => $campaignId)));
		}

		$query_params[] = array('group', array('c.id'));

		if($onlyActive){
			$query_params[] = array('having', array('completed=0'));
		}
    
		
    	$query_params = array_merge($query_params, $extra_params);
    
    	return DBManager::getInstance()->query($query_params);
    }
    
    
    /**
     * Alkalmak box - alkalmak lista
     * 
     * @param unknown_type $campaignId
     * @param unknown_type $extra_params
     */
    public function getCampaignMoment($campaignId, $extra_params = array())
    {
    	$query_params = array(
    		array('select', 'cm.id, cm.moment_datetime, COUNT(tcm.id) AS member_count'),
    		array('from', CampaignMoment::model()->tableName().' cm'),
    		array('leftJoin', array(TicketCampaignMoment::model()->tableName().' tcm', 'tcm.campaign_moment_id = cm.id')),
    		array('where', array('cm.campaign_id=:campaign_id', array(':campaign_id' => $campaignId))),
    		array('group', array('cm.id')),
    	);
    
    	$query_params = array_merge($query_params, $extra_params);
    
    	return DBManager::getInstance()->query($query_params);
    }
    
    
	private function getFreeMembersToCampaignTypes(array $campaignTypeIds, $campaignMomentId){
		$query_params = array(
    		array('select', 't.id, m.id AS member_id, m.name AS member_name, tt.moment_count AS ticket_type_moment_count, t.moment_left AS ticket_moment_left, tcm.id AS ticket_campaign_moment_id, IF(m.id=0, 0, 1) AS free'),
    		array('from', Ticket::model()->tableName().' t'),
    		array('join', array(TicketType::model()->tableName() . ' tt', 't.ticket_type_id = tt.id')),
    		array('join', array(TicketTypeCampaignType::model()->tableName().' ttct', 't.ticket_type_id = ttct.ticket_type_id')),
    		array('join', array(Member::model()->tableName() . ' m', 't.member_id = m.id')),
    		array('leftJoin', array(TicketCampaignMoment::model()->tableName() . ' tcm', 'tcm.ticket_id = t.id AND tcm.campaign_moment_id=:cmid', array(':cmid' => $campaignMomentId))),
    		array('where', array('ttct.campaign_type_id IN ('.implode(', ', $campaignTypeIds).')')),

    	);
		
		$results = DBManager::getInstance()->query($query_params);
		
		return $results;
	}
	
    /**
     * Resztvevok box - resztvevok lista
     * 
     * @param unknown_type $campaignMomentId
     * @param unknown_type $extra_params
     * @return multitype:
     */
    public function getCampaignMember($campaignMomentId, $extra_params = array())
    {
    	$campaignMoment = CampaignMoment::model()->findByPk($campaignMomentId);
    	$campaignId = $campaignMoment->campaign_id;
		
    	if (!$campaignMoment) return array();
    	
		$success_moments_temp = $this->getSuccessMoments($campaignId);

    	$campaignTypeId = $campaignMoment->campaign->campaignTypeDetail->campaign_type_id;
		$requiredCampaignTypeDetailMomentCount = $campaignMoment->campaign->campaignTypeDetail->required_moment_count;
		$campaignTypeDetailMomentCount = $campaignMoment->campaign->campaignTypeDetail->moment_count;
		
		$select_array = array(
			't.id',
			'm.id AS member_id',
			'm.name AS member_name',
			'tt.moment_count AS ticket_type_moment_count',
			't.moment_left AS ticket_moment_left',
			'tcm.id AS ticket_campaign_moment_id',
			'IF(m.id=0, 1, 0) AS free',
			'ttct.is_main',
			'ttct.is_free',
			't.price AS ticket_price',
			't.payed_price',
			'IF(cticket.campaign_id=:t_campaign_id, 1, 0) AS campaign_ticket',// megadja, hogy a bérlet az aktuális campaign-hoz lett-e véve
		);

    	$query_params = array(
    		array('select', implode(', ', $select_array)),
    		array('from', Ticket::model()->tableName().' t'),
    		array('join', array(CampaignTicket::model()->tableName() . ' cticket', 't.id = cticket.ticket_id')),
    		array('join', array(TicketType::model()->tableName() . ' tt', 't.ticket_type_id = tt.id')),
    		array('join', array(TicketTypeCampaignType::model()->tableName().' ttct', 't.ticket_type_id = ttct.ticket_type_id')),
    		array('join', array(Member::model()->tableName() . ' m', 't.member_id = m.id')),
    		array('leftJoin', array(TicketCampaignMoment::model()->tableName() . ' tcm', 'tcm.ticket_id = t.id AND tcm.campaign_moment_id=:cmid', array(':cmid' => $campaignMomentId))),
    		array('where', array('ttct.campaign_type_id=:campaign_type_id 
    								AND ((t.moment_left=0 
    										AND tcm.id IS NOT NULL
    									  ) 
    			                          OR ((t.moment_left!=0)
    											AND t.active_from <= NOW() 
    											AND t.active_to > NOW()
    										  )
										  
    			  						 )', array(':campaign_type_id' => $campaignTypeId, ':t_campaign_id' => $campaignId))),
    	);
    
    	$query_params = array_merge($query_params, $extra_params);

    	$results = DBManager::getInstance()->query($query_params);

		if(!$results['totalCount']){
			return $results;
		}
		else{
			$success_moments = isset($success_moments_temp['data'][$campaignId]) ? $success_moments_temp['data'][$campaignId] : array();

			foreach ($results['data'] as $key => &$data) {
				$data['success_moment_count'] = 0;
				$data['required_moment_count'] = $requiredCampaignTypeDetailMomentCount;
				$data['moment_count'] = $campaignTypeDetailMomentCount;
				
				if(isset($success_moments[$data['member_id']])){
					$data['success_moment_count'] = $success_moments[$data['member_id']];
				}
			}
		}

		return $results;
    }
    
	private function makeUniqueCampaignMember(array $main, array $sub){
		$mainIds = array();
		foreach ($main['data'] as $key => $mvalue) {
			$mainIds[$mvalue['member_id']] = $mvalue['member_id'];
		}
		
		$filteredSubData = array();
		
		foreach ($sub['data'] as $key => $svalue) {
			if(!isset($mainIds[$svalue['member_id']])){
				$filteredSubData[] = $svalue;
			}
		}
		
		foreach ($filteredSubData as $fdata) {
			$main['data'][] = $fdata;
		}
		
		$main['totalCount'] = count($main['data']);
		
		return $main;
	}
	
	/**
	 * 
	 */
	private function getSuccessMoments($campaignId = null){
		$query_params = array(
    		array('select', 'c.id, t.member_id, COUNT(t.member_id) AS success'),
    		array('from', TicketCampaignMoment::model()->tableName().' tcm'),
    		array('join', array(Ticket::model()->tableName() . ' t', 't.id = tcm.ticket_id')),
    		array('join', array(CampaignMoment::model()->tableName() . ' cm', 'cm.id = tcm.campaign_moment_id')),
			array('join', array(Campaign::model()->tableName() . ' c', 'cm.campaign_id = c.id')),
			array('group', array('t.member_id')),
    	);

		$extra_params = array();
		
		if(!is_null($campaignId)){
			$extra_params = array(
				array('where', array('c.id = :campaign_id', array(':campaign_id' => $campaignId))),
			);
		}
		
    	$query_params = array_merge($query_params, $extra_params);
    //print_r($query_params); exit;
    	$temp_result = DBManager::getInstance()->query($query_params);
		
		$result = array('data' => array(), 'totalCount' => $temp_result['totalCount']);
		
		foreach ($temp_result['data'] as $key => $value) {
			$result['data'][$value['id']][$value['member_id']] = $value['success'];
		}
		
		return $result;
	}
    
    /**
     * Alkalmak box - uj alkalom felvetele a kampanyhoz
     * 
     * @param unknown_type $campaign_id
     * @param unknown_type $moment_datetime
     * @return boolean|multitype:string
     */
    public function saveCampaignMoment($campaign_id, $moment_datetime) {
    	$error = array();
    	
    	if($this->campaignMomentDateTimeIsExists($campaign_id, $moment_datetime)){
    		$error[] = 'Ebben a kampányban '.$moment_datetime.' -es alkalom mar van definiálva!';
    		
    		return $error;
    	}
    	
    	$campaign = Campaign::model()->findByPk($campaign_id);
    	$campaign_type_detail_moment_count = (int)$campaign->campaignTypeDetail->moment_count;
    	$campaign_type_detail_id = (int)$campaign->campaignTypeDetail->id;
    	$is_unlimited = $campaign_type_detail_moment_count == null ? true : false;
    	$campaign_moment_count = (int)CampaignMoment::model()->count('campaign_id =:campaign_id', array(':campaign_id' => $campaign_id));
    	
    	$campaign_moment = new CampaignMoment();
    	$campaign_moment->campaign_id = $campaign_id;
    	
    	$campaign_type_detail_moment_id = null;


    		if($is_unlimited){
    			$campaign_type_detail_moment = CampaignTypeDetailMoment::model()->find('campaign_type_detail_id=:ctd_id AND moment_number=:mn', array(
    				':ctd_id' => $campaign_type_detail_id,
    				':mn' => 1,	
    			));

   				$campaign_type_detail_moment_id = $campaign_type_detail_moment->id;
    		}
    		elseif (!$is_unlimited && $campaign_type_detail_moment_count>$campaign_moment_count){
    			$actual_campaign_moment = $campaign_moment_count+1;
//var_dump($campaign_type_id);
//var_dump($actual_campaign_moment); exit;
    			$campaign_type_detail_moment = CampaignTypeDetailMoment::model()->find('campaign_type_detail_id=:ctd_id AND moment_number=:mn', array(
    				':ctd_id' => $campaign_type_detail_id,
    				':mn' => $actual_campaign_moment,	
    			));

    			$campaign_type_detail_moment_id = $campaign_type_detail_moment->id;

    			if($campaign_type_detail_moment_count == $actual_campaign_moment){
    				//elfogytak a kampany alkalmak , lezarjuk az adott nap ejfellel
    				$this->closeCampaign($campaign_id);
    			}
    		}
    		else{
    			$error[] = 'Ebben a kampányban '.$campaign_type_detail_moment_count.' alkalom van definiálva!';
    		}
    	
    	$campaign_moment->campaign_type_detail_moment_id = $campaign_type_detail_moment_id;
    	$campaign_moment->moment_datetime = $moment_datetime;
    	
    	if(empty($error)){
    		$campaign_moment->save();
    	}
    	
    	return $error;
    }
    
    /**
     * Lezarja az aktualis kampanyt
     * 
     * @param unknown_type $campaign_id
     */
    private function closeCampaign($campaign_id, $date = null) {
    	if (is_null($date)){
    		$date = date('Y-m-d');
    	}
    	Campaign::model()->updateByPk($campaign_id, array('end_datetime' => $date . ' 23:59:59'));
    }
    
    /**
     * Megnezi, hogy az adott kampanyhoz az adott idopontban letezik-e alkalom
     * 
     * @param unknown_type $campaign_id
     * @param unknown_type $moment_datetime
     */
    public function campaignMomentDateTimeIsExists($campaign_id, $moment_datetime) {
    	return CampaignMoment::model()->count('campaign_id=:cid AND moment_datetime=:mdt', array(
    		':cid' => $campaign_id,
    		':mdt' => $moment_datetime,	
    	));
    }
    
    /**
     * Ellenorzi, hogy az adott membernek megvan-e az adott kampanytipushoz kello kampany tipusa(i)
     * 
     * @param unknown_type $memberId
     * @param unknown_type $campaignTypeId
     */
    public function hasRequiredCampaignTypeForNewCampaignType($memberId, $campaignTypeId){
    	$requiredCampaignTypes = $this->getRequiredCampaignTypeForCampaignType($campaignTypeId);

    	if (!$requiredCampaignTypes) return true;
    	
    	$memberDoneCampaignTypes = $this->getExistsCampaignTypesToMemberIds($memberId);
    	
    	if (!$memberDoneCampaignTypes) $memberDoneCampaignTypes = array();
    	
    	$result = array_diff($requiredCampaignTypes, $memberDoneCampaignTypes);

    	if (!empty($result)) {
    		$query_params = array(
    				array('select', 'dt.name AS dance_type_name, ct.name AS campaign_type_name'),
    				array('from', CampaignType::model()->tableName().' ct'),
    				array('join', array(DanceType::model()->tableName() . ' dt', 'ct.dance_type_id = dt.id')),
    				array('where', array('ct.id IN ('.implode(', ', $result).')')),
    		);
    		 
    		$result = DBManager::getInstance()->query($query_params);
    		
    		$res_arr = array();
    		
    		foreach ($result['data'] AS $res){
    			$res_arr[] = $res['dance_type_name'].' '.$res['campaign_type_name'];
    		}
    		
    		return $res_arr;
    		
    	}
    	
    	return true;
    }
    
    /**
     * Visszaadja az adott kampanytipus kezdeshez milyen kotelezo meglevo kampany tipusok kellenek
     * 
     * @param unknown_type $campaignTypeId
     * @return boolean|multitype:unknown
     */
    private function getRequiredCampaignTypeForCampaignType($campaignTypeId) {
    	$query_params = array(
    			array('select', 'require_campaign_type_id'),
    			array('from', CampaignTypeRequire::model()->tableName().' ctr'),
    			array('where', array('ctr.campaign_type_id=:ctid', array(':ctid' => $campaignTypeId))),
    	);
    	
    	
    	$result = DBManager::getInstance()->query($query_params);
    	
    	if (!$result['totalCount']) return false;
    	
    	$campaignTypeIds = array();
    	
    	foreach ($result['data'] as $data) {
    		$campaignTypeIds[] = $data['require_campaign_type_id'];
    	}
    	
    	return $campaignTypeIds;
    }
    
    /**
     * Visszadja, hogy az adott membernek milyen kampany tipusai vannak meg, idk
     * 
     * @param unknown_type $memberId
     * @return boolean|multitype:unknown
     */
    private function getExistsCampaignTypesToMemberIds($memberId) {    	
    	$result = $this->getDoneCampaignTypeToMember($memberId);

    	if(!$result['totalCount']) return false;
    	
    	$existsCampaignTypeIds = array();
    	 
    	foreach ($result['data'] as $data) {
    		$existsCampaignTypeIds[] = $data['id'];
    	}
    	 
    	return $existsCampaignTypeIds;
    }
    
    /**
     * Visszadja, hogy az adott membernek milyen kampany tipusai vannak meg, adatok
     * 
     * @param unknown_type $memberId
     * @return boolean|unknown
     */
    public function getDoneCampaignTypeToMember($memberId, array $extra_params = array()) {
    	$query_params = array(
    			array('select', 'ct.id, dt.name AS dance_type_name, ct.name'),
    			array('from', MemberCampaignType::model()->tableName().' mct'),
    			array('join', array(CampaignType::model()->tableName() . ' ct', 'mct.campaign_type_id = ct.id')),	
    			array('join', array(DanceType::model()->tableName() . ' dt', 'ct.dance_type_id = dt.id')),
    			array('where', array('mct.member_id=:mid AND mct.done=:done', array(':mid' => $memberId, ':done' => 1))),
    	);
    	
    	$query_params = array_merge($query_params, $extra_params);
    	
    	$result = DBManager::getInstance()->query($query_params);
    	
    	//if (!$result['totalCount']) return false;
    	
    	return $result;
    }
    
    /**
     * Visszadja, az adott membernek osszes kampany tipussal osszekotott sorat
     *
     * @param unknown_type $memberId
     * @return boolean|unknown
     */
    public function getAllCampaignTypeToMember($memberId, array $extra_params = array()) {
    	$query_params = array(
    			array('select', 'mct.id, ct.campaign_type_id, dt.name AS dance_type_name, ct.name'),
    			array('from', MemberCampaignType::model()->tableName().' mct'),
    			array('join', array(CampaignType::model()->tableName() . ' ct', 'mct.campaign_type_id = ct.id')),
    			array('join', array(DanceType::model()->tableName() . ' dt', 'ct.dance_type_id = dt.id')),
    			array('where', array('mct.member_id=:mid', array(':mid' => $memberId))),
    	);
    	 
    	$query_params = array_merge($query_params, $extra_params);
    	 
    	$result = DBManager::getInstance()->query($query_params);
    	 
    	//if (!$result['totalCount']) return false;
    	 
    	return $result;
    }
    
    /**
     * Resztvevok box - veszunk fel uj berletet, automatikusan alkalomhoz koti
     * 
     * @param unknown_type $ticketId
     * @param unknown_type $campaignMomentId
     * @param unknown_type $date
     */
    public function addTicketToCampaignMoment($ticketId, $campaignMomentId, $date){
    	$ticket = Ticket::model()->findByPk($ticketId);
    	$memberId = $ticket->member->id;
    	
    	$tcm = new TicketCampaignMoment();
    	$tcm->campaign_moment_id = $campaignMomentId;
    	$tcm->ticket_id = $ticketId;
    	$tcm->moment_datetime = $date;
    	 
    	if($tcm->save()){
    		$ticket = Ticket::model()->findByPk($ticketId);
    		$moment_left = $ticket->moment_left;
    		$ticket->moment_left = $moment_left - 1;
    		$ticket->save();
    		
    		$this->setMemberCampaignTypeDone($memberId, $campaignMomentId);
    	}
    }
    
    /**
     * Megallapitja, hogy az adott membernek megvannak-e az adott kampanytipusnal definialt kotelezo alkalmak
     * pl. definialva van kotelezo alkalmakkent a 1,2,5-7 ,visszadja,hogy ezek megvannak-e
     * 
     * @param unknown_type $memberId
     * @param unknown_type $campaignTypeId
     * @return boolean
     */
    public function hasDefinedRequiredMoments($memberId, $campaignTypeDetailId){
    	$query_params = array(
    			array('select', 'moment_number, ctd.required_moments, ctd.moment_count, ctd.required_moment_count'),
    			array('from', TicketCampaignMoment::model()->tableName().' tcm'),
    			array('join', array(CampaignMoment::model()->tableName() . ' cm', 'tcm.campaign_moment_id = cm.id')),
    			array('join', array(CampaignTypeDetailMoment::model()->tableName().' ctdm', 'ctdm.id = cm.campaign_type_detail_moment_id')),
    			array('join', array(CampaignTypeDetail::model()->tableName().' ctd', 'ctd.id = ctdm.campaign_type_detail_id')),
    			array('join', array(Ticket::model()->tableName() . ' t', 't.id = tcm.ticket_id')),
    			array('join', array(Member::model()->tableName() . ' m', 't.member_id = m.id')),
    			array('where', array('ctdm.is_required=:is_required
    								AND 
    								t.member_id=:member_id
    								AND
    								ctdm.campaign_type_detail_id=:ctdid
    								', array(':ctdid' => $campaignTypeDetailId,
    										 ':member_id' => $memberId,
    										 ':is_required' => 1))),
    			array('order', 'ctdm.moment_number'),
    	);
    	
    	
    	$result = DBManager::getInstance()->query($query_params);

    	if(!$result['totalCount']){
    		return true;
    	}

    	$hasDefinedMomentNumbers = array();
    	
    	foreach ($result['data'] as $row) {
    		$hasDefinedMomentNumbers[$row['moment_number']] = $row['moment_number'];
    	}
    	
    	$campaignTypeDetailMomentCount = $result['data'][0]['moment_count'];
    	$campaignTypeDetailRequiredMoments = $result['data'][0]['required_moments'];
    	
    	$requiredMomentsArray = $this->getRequiredMomentsArray($campaignTypeDetailMomentCount, $campaignTypeDetailRequiredMoments);
    	
    	$definedRequiredMomentCount = count($requiredMomentsArray);
    	
    	$hasReqMomentCount = 0;
    	foreach ($requiredMomentsArray as $reqMoment){
    		if(isset($hasDefinedMomentNumbers[$reqMoment])) $hasReqMomentCount++;
    		
    		if($definedRequiredMomentCount == $hasReqMomentCount) return true;
    	}
    	
    	return false;
    }
    
    /**
     * Beallitja az adott alkalom alapjan az adott membernek a done erteket ,azt jelolni, hogy teljesitette-e a campany tipust
     * 
     * @param unknown_type $memberId
     * @param unknown_type $campaignMomentId
     */
    public function setMemberCampaignTypeDone($memberId, $campaignMomentId) {
    	
		
    	$requiredMomentCount = CampaignMoment::model()->findByPk($campaignMomentId)->campaign->campaignTypeDetail->required_moment_count;
    	

    	$requiredMoments = CampaignMoment::model()->findByPk($campaignMomentId)->campaign->campaignTypeDetail->required_moments;
    	$campaignTypeDetailId = CampaignMoment::model()->findByPk($campaignMomentId)->campaign->campaignTypeDetail->id;
		$campaignTypeId = CampaignMoment::model()->findByPk($campaignMomentId)->campaign->campaignTypeDetail->campaignType->id;

		$momentCount = CampaignMoment::model()->findByPk($campaignMomentId)->campaign->campaignTypeDetail->moment_count;
		
		$isInfiniti = !is_null($momentCount) || (int)$momentCount > 0 ? true : false ;
		
    	$completedCampaignTypeDetailMomentCount = $this->getCampaignTypeDetailMomentCountForMember($campaignTypeDetailId, $memberId, $isInfiniti);
    	
    	$campaignTypeForMemberObj = $this->campaignTypeForMemberData($campaignTypeId, $memberId);

    	$hasDefinedRequiredMoments = $this->hasDefinedRequiredMoments($memberId, $campaignTypeDetailId);

    	//ha megvan a kotelezo alkalmak szama es a kotelezonek definialt alkalmak koztuk vannak akkor done flaget beallitja 1re
    	if (((int)$requiredMomentCount <= (int)$completedCampaignTypeDetailMomentCount) && $hasDefinedRequiredMoments) {
    		if(is_null($campaignTypeForMemberObj)){
    			$mct = new MemberCampaignType();
    			$mct->member_id = $memberId;
    			$mct->campaign_type_id = $campaignTypeId;
    			$mct->start_date = sfDate::getInstance()->formatDbDateTime();
    		}
    		else{
    			$mct = $campaignTypeForMemberObj;
    		}
    	
    		if(!$mct->done){
    			$mct->end_date = sfDate::getInstance()->formatDbDateTime();
    			$mct->done = 1;
    			$mct->save();
    		}

    	}//ha kevesebb a kotelezo alkalmaknal es mar megvolt vagy a kotelezonek definialt alkalmak kozul esik ki valamelyik, akkor done 0
    	elseif(($requiredMomentCount > $completedCampaignTypeDetailMomentCount) || !$hasDefinedRequiredMoments){
    		if(!is_null($campaignTypeForMemberObj) && $campaignTypeForMemberObj->done){
    			$campaignTypeForMemberObj->done = 0;
    			$campaignTypeForMemberObj->end_date = null;
    			$campaignTypeForMemberObj->save();
    		}
    		elseif(is_null($campaignTypeForMemberObj)){
    			$mct = new MemberCampaignType();
    			$mct->member_id = $memberId;
    			$mct->campaign_type_id = $campaignTypeId;
    			$mct->start_date = sfDate::getInstance()->formatDbDateTime();
    			$mct->done = 0;
    			$mct->save();
    		}
    	}
    }
    
    private function getCampaignTypeFromTicket($ticketId){
    	$query_params = array(
    			array('select', 'ctd.campaign_type_id'),
    			array('from', Ticket::model()->tableName().' t'),
    			array('join', array(CampaignTicket::model()->tableName() . ' cti', 'cti.ticket_id = t.id')),
    			array('join', array(Campaign::model()->tableName() . ' c', 'cti.campaign_id = c.id')),
    			array('join', array(CampaignTypeDetail::model()->tableName().' ctd', 'ctd.id = c.campaign_type_detail_id')),
    			array('where', array('t.id=:tid', array(':tid' => $ticketId))),
    	);
    	 
    	$result = DBManager::getInstance()->query($query_params);
    	
    	return $result['data'][0]['campaign_type_id'];
    }
    
    private function getCampaignTypeFromCampaignMoment($campaignMomentId){
    	$query_params = array(
    			array('select', 'ctd.campaign_type_id'),
    			array('from', CampaignMoment::model()->tableName().' cm'),
    			array('join', array(Campaign::model()->tableName() . ' c', 'cm.campaign_id = c.id')),
    			array('join', array(CampaignTypeDetail::model()->tableName().' ctd', 'ctd.id = c.campaign_type_detail_id')),
    			array('where', array('cm.id=:cmid', array(':cmid' => $campaignMomentId))),
    	);
    
    	$result = DBManager::getInstance()->query($query_params);
    	 
    	return $result['data'][0]['campaign_type_id'];
    }
    
    /**
     * Resztvevok box - be vagy ki pipal egy resztvevot
     * 
     * @param unknown_type $ticketId
     * @param unknown_type $campaignMomentId
     * @param unknown_type $checked
     * @param unknown_type $momentDateTime
     */
    public function handleCampaignMomentCheck($ticketId, $campaignMomentId, $checked, $momentDateTime = null, $isFree = 0){    	
    	$momentDateTime = (is_null($momentDateTime)) ? sfDate::getInstance()->formatDbDateTime() : $momentDateTime ;

    	$ticket = Ticket::model()->findByPk($ticketId);    	
    	$memberId = $ticket->member->id;

    	$isExistRow = TicketCampaignMoment::model()->count('ticket_id =:tid AND campaign_moment_id=:cmid', array(
    		'tid' => $ticketId,
    		'cmid' => $campaignMomentId,	
    	));
    	
    	$campaignMomentCampaignTypeId = $this->getCampaignTypeFromCampaignMoment($campaignMomentId);
    	$ticketCampaignTypeId = $this->getCampaignTypeFromTicket($ticketId);
		
		//var_dump(array($ticket->id,$checked, $isExistRow)); exit;
    	//bepipal, tehat reszt vesz
    	if($checked && !$isExistRow){
    		if($ticket->moment_left == 0 && !$isFree) throw new Exception('Hiba', 'A bérleten már nics szabad hely!');
    		
    		$tcm = new TicketCampaignMoment();
    		$tcm->ticket_id = $ticketId;
    		$tcm->campaign_moment_id = $campaignMomentId;
    		$tcm->moment_datetime = $momentDateTime;
    		
    		if($tcm->save()){
    			if(!$isFree){
    				$ticket->moment_left = $ticket->moment_left - 1;
				}
    		}
    		else{
    			throw new Exception(__METHOD__.'Nem sikerült a mentés!');
    		}
    	}//kipipal, tehat megsem vesz reszt
    	elseif(!$checked && $isExistRow){
    		TicketCampaignMoment::model()->deleteAll('ticket_id =:tid AND campaign_moment_id=:cmid', array(
	    		'tid' => $ticketId,
	    		'cmid' => $campaignMomentId,	
    		));
    		
			if(!$isFree){
    			$ticket->moment_left = $ticket->moment_left + 1;
			}
    	}
    	
    	if(!$ticket->save()){
    		print_r(ModelManager::getInstance()->getModelErrors($ticket)); exit;
    	}
    	
    	if($ticketCampaignTypeId == $campaignMomentCampaignTypeId){
    	
    		$this->setMemberCampaignTypeDone($memberId, $campaignMomentId);
    	
    	}
    }
    
    /**
     * Lekeri egy adott membernek egy adott kampany tipusbol, hany kulonbozo alkalmon volt jelen,
     * ha 5x volt az 1. alkalmon az 1db alkalomnak szamit, kiveva ha vegtelen, akkor minden alkalom szamit
     * 
     * @param unknown_type $campaignTypeId
     * @param unknown_type $memberId
	 * @param boolean true akkor végtelen alakmak szama
     */
    public function getCampaignTypeDetailMomentCountForMember($campaignTypeDetailId, $memberId, $isInfiniti = false) {       	
    	$query_params = array(
			array('select', 'cm.campaign_type_detail_moment_id'),
			array('from', TicketCampaignMoment::model()->tableName().' tcm'),
			array('join', array(Ticket::model()->tableName() . ' t', 'tcm.ticket_id = t.id')),
			array('join', array(CampaignMoment::model()->tableName() . ' cm', 'tcm.campaign_moment_id = cm.id')),
			array('join', array(Campaign::model()->tableName() . ' c', 'cm.campaign_id = c.id')),
			array('where', array('c.campaign_type_detail_id =:ctdid AND member_id =:mid', array(':ctdid' => $campaignTypeDetailId, ':mid' => $memberId))),
    	);

		if(!$isInfiniti){
			$query_params = array_merge($query_params, array('group', array('cm.campaign_type_detail_moment_id')));
		}

    	$result = DBManager::getInstance()->query($query_params);

    	return $result['totalCount'];
    }
    
    /**
     * Megnezi, hogy az adott membernek megvane az adott campaign_type
     * 
     * @param unknown_type $campaignTypeId
     * @param unknown_type $memberId
     * @return null, ha nincs ilyen sor, 0 , ha van sor,de nincs meg, 1 ha van sor es megvan
     */
    public function campaignTypeForMemberData($campaignTypeId, $memberId) {
    	$obj = MemberCampaignType::model()->find('member_id=:mid AND campaign_type_id=:ctid', array(
    		':mid' => $memberId,
    		':ctid' => $campaignTypeId,	
    	));
    	
    	if($obj){
    		return $obj;
    	}
    	
    	return null;
    }
    
    /**
     * Uj kampany felvetele
     * @see BaseModelManager::save()
     */
    public function save($model_name, $params, $form_name){
    	
    	$existsCid = $this->campaignIsExists($params['campaign_type_detail_id'], $params['location_id'], $params['start_datetime']);
    	
    	$save = false;
    	
    	if($this->isEdit($params) && (($existsCid && ($existsCid == $params['id'])) || !$existsCid)){
    		$save = true;
    	}
    	
    	if(!$this->isEdit($params) && !$existsCid){
    		$save = true;
    	}
    	
    	
    	if ($save) {
    		return parent::save($model_name, $params, $form_name);
    	}
    	else{
    		return array(
    			'success'=>true,
    			'message'=>'Nem sikerült felvenni a megadott kampányt! Már létezik ilyen kampány!',
    		);
    	}
    }
    
    /**
     * Ellenorzi, hogy van-e mar adott kampany tipusu kampany adott helyen es idoben
     * 
     * @param unknown_type $campaign_type_id
     * @param unknown_type $location_id
     * @param unknown_type $start_datetime
     */
    private function campaignIsExists($campaign_type_detail_id, $location_id, $start_datetime){
    	$c = Campaign::model()->find('campaign_type_detail_id=:ctdid AND location_id=:lid AND start_datetime=:sdt', array(
    		':ctdid' => $campaign_type_detail_id,
    		':lid' => $location_id,
    		':sdt' => $start_datetime,	
    	));
    	
    	if(!is_null($c)){
    		return $c->id;
    	}
    	
    	return false;
    }
    
    /**
     * Kampany torlese
     * 
     * @param unknown_type $campaignId
     */
    public function deleteCampaign($campaignId) {
    	$errors = array();
    	
    	if(CampaignMoment::model()->count('campaign_id=:cid', array(':cid' => $campaignId))){
    		$errors[] = 'Addig nem törölhető amíg tartozik alá alkalom!';
    	}
    	
    	if (!empty($errors)) {
            return array(
                'success'=>true,
            	'error' => 1,
                'message'=>Yii::t('msg' ,'Kampány törlése sikertelen!'),
                'errors'=>$errors
            );
        }
        
        $response_success_true = array(
        		'success'=>true,
        		'error' => 0,
        		'message'=>Yii::t('msg' ,'Kampány sikeresen törölve.')
        );
        $response_success_false = array(
        		'success'=>false,
        		'error' => 1,
        		'message'=>Yii::t('msg' ,'Kampány törlése sikertelen!'),
        		'errors'=>array()
        );
        
        $rows_deleted = Campaign::model()->deleteByPk($campaignId);
        
        return $rows_deleted == 1 ? $response_success_true : $response_success_false;
    }
    
    /**
     * Kampany alkalom torlese
     *
     * @param unknown_type $campaignMomentId
     */
    public function deleteCampaignMoment($campaignMomentId) {
    	$errors = array();
    	 
    	if(TicketCampaignMoment::model()->count('campaign_moment_id=:cmid', array(':cmid' => $campaignMomentId))){
    		$errors[] = 'Addig nem törölhető amíg tartozik alá bérlet!';
    	}
    	 
    	if (!empty($errors)) {
    		return array(
    				'success'=>false,
    				'error' => 1,
    				'message'=>Yii::t('msg' ,'Kampány alkalom törlése sikertelen!'),
    				'errors'=>$errors
    		);
    	}
    
    	$response_success_true = array(
    			'success'=>true,
    			'error' => 0,
    			'message'=>Yii::t('msg' ,'Kampány alkalom sikeresen törölve.')
    	);
    	$response_success_false = array(
    			'success'=>false,
    			'error' => 1,
    			'message'=>Yii::t('msg' ,'Kampány alkalom törlése sikertelen!'),
    			'errors'=>array()
    	);
    
    	$rows_deleted = CampaignMoment::model()->deleteByPk($campaignMomentId);
    
    	return $rows_deleted == 1 ? $response_success_true : $response_success_false;
    }
	
	/**
	 * visszadja, hogy a kampany(ok) elkezdesehez milyen kampany(ok) szuksegesek
	 */
	public function needToAddAll(){
		$result = array();
		
		$all = CampaignTypeRequire::model()->findAll();
		
		foreach ($all as $ctr) {
			$issetCtId = isset($result[$ctr->campaign_type_id]);
			
			if(!$issetCtId){
				$result[$ctr->campaign_type_id][$ctr->group] = array();
			}
			
			$result[$ctr->campaign_type_id][$ctr->group][] = $ctr->require_campaign_type_id;

		}
		
		ksort($result);
		
		return $result;
	}
	
	/**
	 * visszadja, hogy a kampany meglete milyen alsobb kampanyokon valo reszvetelhez jogosit fel
	 */
	private function whatShouldBe(array &$container, $campaignTypeId){
		$rows = CampaignTypeRequire::model()->findAll('require_campaign_type_id =:rctid', array(':rctid' => $campaignTypeId));
		
		foreach ($rows as $data) {
			$container[$data->campaign_type_id] = $data->campaign_type_id;
			$this->whatShouldBe($container, $data->campaign_type_id);
		}
	}
	
	/**
	 *
	 * visszadja, hogy a kampany meglete milyen alsobb kampanyokon valo reszvetelhez jogosit fel
	 */
	public function getWhatShouldBe($campaignTypeId){
		$container = array();
		
		CampaignManager::getInstance()->whatShouldBe($container, $campaignTypeId);
		
		$nedded = CampaignTypeRequire::model()->findAll('campaign_type_id =:ctid', array(':ctid' => $campaignTypeId));
		
		foreach ($nedded as $value) {
			$container[$value->require_campaign_type_id] = $value->require_campaign_type_id;
		}
		
		if(isset($container[$campaignTypeId])){
			unset($container[$campaignTypeId]);
		}
		
		sort($container);
		
		return $container; 
	}
	
	public function getCampaignPriceRulesList($campaignId, $withPrices = false){
		$n = CampaignPriceRules::model()->find('campaign_id =:cid AND lft=:lft',array(':cid' => $campaignId, ':lft' => 1));

		$campaignPrice = $this->getCampaignTicketPrice($campaignId);
		$expense = $this->getCampaignExpensePrice($campaignId);

		$campaignPrice = $campaignPrice - $expense;

		$children = CampaignPriceRules::model()->findByPk($n->id)->children()->findAll();
		
		$data = $this->priceRulesGetChildren($children, $campaignPrice, $expense);
		 
		$data = array(
			'data' => array(
				array(
					'id'  => $n->id,
					'text' => $n->name,
					'link_id' => $n->link_id,
					'full_price' => $campaignPrice,
					'price_type' => $n->price_type,
					'expense_price' => 0,
					'data' => $data,
					'expanded' => true
				)
			)
		);
		
		
		$this->markLeaves($data);

		return $data;
	}
	
	protected function markLeaves(&$node)
    {
        // ha vannak gyermekei a vizsgált node-nak, rekurzívan mindegyikre meghívjuk a fv-t
        if (isset($node['data']) && count($node['data'])) {
        	$node['expanded'] = true;
            foreach ($node['data'] as &$child) $this->markLeaves($child);
        // ha nincs, akkor beállítjuk, hogy ő egy levél
        } else {
            $node['leaf'] = 'true';
            //$node['iconCls'] = 'x-tree-icon-parent';
        }

    }
	
    private function priceRulesGetChildren($children, $parentPrice, $expense){
    	$result = array();
	    foreach($children as $i => $child) {
	    	$percent = $child->percent;
			$price = $child->price;
	    	$full_price = 0;
			$expensePrice = 0;
			
			if($parentPrice>0){
			
		    	if(!empty($percent)){
		    		$full_price =  ($parentPrice/100) * $percent;
		    	}
				elseif(!empty($price) && $child->price_type != Campaign::EXPENSE_NAME){
					$full_price = ($parentPrice>=$price) ? $price : $parentPrice ;
				}
			
			}
			
			//kiadás első ág
			if($expense>0 && $child->price_type == Campaign::EXPENSE_NAME){
				$expensePrice = $expense;
				$expense = 0;
			}
			elseif ($child->price_type == Campaign::EXPENSE_NAME) {
				$expensePrice = $price;
			}
			
	        $category_r = array(
	        	'id'            => $child->id,
	            'text'          => $child->name,
	            'link_id'       => $child->link_id,
	            'price'         => $price,
	            'full_price'    => $full_price,
	            'price_type'    => $child->price_type,
	            'expense_price' => $expensePrice,
	            'percent'       => $percent,
	        );          
	        $result[$i] = $category_r;
	        $new_children = $child->children()->findAll();
	        if($new_children) {
	            $result[$i]['data'] = $this->priceRulesGetChildren($new_children, $full_price, $expense);
	        }           
	    }
	    return $result_items = $result;
    }
	
	public function deleteCampaignTypeDetail($campaignTypeDetailId){
		$response = array(
    		'success'=>true,
    		'error' => 0,
    		'message'=>Yii::t('msg' ,'Kampány sikeresen törölve.')
        );
		
		if(!Campaign::model()->count('campaign_type_detail_id=:ctdid', array(':ctdid' => $campaignTypeDetailId))){
			CampaignTypeDetail::model()->deleteByPk($campaignTypeDetailId);
			CampaignTypeDetailMoment::model()->deleteAll('campaign_type_detail_id=:ctdid', array(':ctdid' => $campaignTypeDetailId));
		}
		else{
			$response = array(
	    		'success'=>false,
	    		'error' => 0,
	    		'message'=>Yii::t('msg' ,'Nem lehet törölni, mert van ilyen kampány típussal indítva kampány!')
        	);
		}
		
		return $response;
	}
	
	public function startPriceRulesForCampaign($campaignId){
		if(!CampaignPriceRules::model()->count('campaign_id=:cid', array(':cid' => $campaignId))){
			
			$cdata = CampaignManager::getInstance()->getCampaigns(array(), $campaignId);
			
			$moment_text = (is_null($cdata['data'][0]['campaign_type_detail_moment_count']) || 
							$cdata['data'][0]['campaign_type_detail_moment_count'] == 0
			) ? 'végtelen' : $cdata['data'][0]['campaign_type_detail_moment_count'] . ' alkalmas' ;
			
			$n = new CampaignPriceRules();
			$n->name = $cdata['data'][0]['dance_type_name'].
						 ' '.
						 $cdata['data'][0]['campaign_type_name'].
						 ' '.
						 $moment_text.
						 ' (Kezdés : '.
						 $cdata['data'][0]['start_date'].' )';
			$n->campaign_id = $campaignId;
			$n->makeRoot(false);
			
			$exp = new CampaignPriceRules();
			$exp->name = Yii::t('msg', 'Kiadás');
			$exp->campaign_id = $campaignId;
			$exp->price_type = Campaign::EXPENSE_NAME;
			$exp->appendTo($n);
			
			$inc = new CampaignPriceRules();
			$inc->name = Yii::t('msg', 'Bevétel');
			$inc->campaign_id = $campaignId;
			$inc->price_type = Campaign::INCOME_NAME;
			$inc->percent = 100;
			$inc->appendTo($n);
			
		}
	}

	public function getRightToComissionUsers(array $extra_params = array())
    {
    	$user_group_id = Yii::app()->params['right_to_comission_user_group_id'];
		
    	$query_params = array(
    		array('select', 'u.id, u.username AS name'),
    		array('from', User::model()->tableName().' u'),
    		array('join', array(UserUserGroup::model()->tableName() . ' uug', 'uug.user_id = u.id')),
    		array('where', array("uug.user_group_id=".$user_group_id)),
    	);
    
    	$query_params = array_merge($query_params, $extra_params);
    
    	return DBManager::getInstance()->query($query_params);
    }
	
	public function saveCampaignPriceUser(array $data){
		$parent_id = $data['tree_parent_id'];
		$edit = false;
		
		$user = User::model()->findByPk($data['user_id']);
		
		if($data['id']){
			$edit = true;
		}
		
		if(!$edit){
			$parent = CampaignPriceRules::model()->findByPk($parent_id);
			$priceType = $parent->price_type;

			$b = new CampaignPriceRules();
		}
		else{
			$b = CampaignPriceRules::model()->findByPk($data['id']);
			$priceType = $b->price_type;
		}
		
		$b->name = $user->username;
		$b->price = (empty($data['price'])) ? null : $data['price'] ;
		$b->percent = (empty($data['percent'])) ? null : $data['percent'] ;
		$b->link_id = $data['user_id'];
		$b->link_type = 'user';
		$b->price_type = $priceType;
		
		if(!$edit){
			$b->appendTo($parent);
		}
		else{
			$b->saveNode();
		}
		
		return array('success'=>true, 'error'=>0);
	}
	
	public function saveCampaignPriceGeneral(array $data){
		$parent_id = $data['tree_parent_id'];
		$edit = false;
		
		if(empty($parent_id) && $data['id']){
			$edit = true;
		}
		 
		if(!$edit){
			$parent = CampaignPriceRules::model()->findByPk($parent_id);
			$priceType = $parent->price_type;

			$b = new CampaignPriceRules();
		}
		else{
			$b = CampaignPriceRules::model()->findByPk($data['id']);
			$priceType = $b->price_type;
		}

		$b->name = $data['name'];
		$b->price = (empty($data['price'])) ? null : $data['price'] ;
		$b->percent = (empty($data['percent'])) ? null : $data['percent'] ;
		$b->link_id = null;
		$b->link_type = null;
		$b->price_type = $priceType;
		
		if(!$edit){
			$b->appendTo($parent);
		}
		else{
			$b->saveNode();
		}
		
		return array('success'=>true, 'error'=>0);
	}
	
	public function addTicketToCampaign($campaignId, $ticketId){
		$ct = new CampaignTicket();
		$ct->campaign_id = $campaignId;
		$ct->ticket_id = $ticketId;
		$ct->save();
	}
	
	private function getCampaignTicketPrice($campaignId){
		$query_params = array(
    		array('select', 'SUM(t.payed_price) AS price'),
    		array('from', CampaignTicket::model()->tableName().' ct'),
    		array('join', array(Ticket::model()->tableName() . ' t', 'ct.ticket_id = t.id')),
    		array('where', array("ct.campaign_id=:campaign_id", array(':campaign_id' => $campaignId))),
    	);
    
		$result = DBManager::getInstance()->query($query_params);
	
    	return (!empty($result)) ? $result['data'][0]['price'] : 0 ;
	}
	
	private function getCampaignExpensePrice($campaignId){
		$query_params = array(
    		array('select', 'SUM(cpr.price) AS price'),
    		array('from', CampaignPriceRules::model()->tableName().' cpr'),
    		array('where', array("cpr.price_type=:pt AND cpr.campaign_id=:campaign_id", array(':campaign_id' => $campaignId, ':pt' => Campaign::EXPENSE_NAME))),
    	);
    
		$result = DBManager::getInstance()->query($query_params);
	
    	return (!empty($result)) ? $result['data'][0]['price'] : 0 ;
	}
	
	public function hasTicketCampaignToMember($campaignId, $memberId){
		$query_params = array(
			array('select', 't.id'),
			array('from', Ticket::model()->tableName().' t'),
			array('join', array(CampaignTicket::model()->tableName() . ' ct', 'ct.ticket_id = t.id')),
			array('where', array("ct.campaign_id=:campaign_id AND t.member_id=:member_id AND ((t.moment_left!=0)
    											AND t.active_from <= NOW() 
    											AND t.active_to > NOW()
    										  )", array(':campaign_id' => $campaignId, ':member_id' => $memberId))),
		);
		
		$result = DBManager::getInstance()->query($query_params);
		
		return ($result['totalCount'] > 0) ? true : false ;
	}
	
	public function getPriceRulesSablonList(){
		$query_params = array(
			array('select', 'id, name'),
			array('from', PriceRulesSablon::model()->tableName()),
		);
		
		$result = DBManager::getInstance()->query($query_params);
		
		return $result;
	}
	
	public function savePriceRulesSablon($prs_id, $prs_raw_value, $campaign_id){
		//print_r(func_get_args()); exit;
		$end_message = '';
		
		if($prs_id == $prs_raw_value){
			if(PriceRulesSablon::model()->count('name=:name', array(':name' => $prs_raw_value))){
				return array(
					'success'=>false,
					'error' => 1,
					'message'=>Yii::t('msg' ,'Ezzel a névvel már létezik szabály!'),
					'errors'=>array()
				);
			}
			
			$prs = new PriceRulesSablon();
			$prs->name = $prs_raw_value;
			if($prs->save()){
				$price_rules_sablon_id = $prs->id;
				$end_message = 'Az új szabály létrehozása sikeres!';
			}
			else{
				return array(
	    			'success'=>false,
	    			'error' => 1,
	    			'message'=>Yii::t('msg' ,'Az új szabály létrehozása sikertelen!'),
	    			'errors'=>array()
		    	);
			}
		}
		else{
			PriceRulesSablonDetail::model()->deleteAll('price_rules_sablon_id=:prsid', array(':prsid' => $prs_id));
			$price_rules_sablon_id = $prs_id;
			$end_message = 'A szabály mentése sikeres!';
		}
		
		$sql = "INSERT INTO price_rules_sablon_detail (campaign_id, lft, rgt, level, name, price, percent,link_id,link_type,price_type,price_rules_sablon_id)
				  SELECT campaign_id, lft, rgt, level, name, price, percent,link_id,link_type,price_type,".$price_rules_sablon_id."
				  FROM campaign_price_rules 
			      WHERE campaign_id =". $campaign_id;
		
		if(!Yii::app()->db->createCommand($sql)->execute()){
			return array(
				'success'=>false,
				'error' => 1,
				'message'=>Yii::t('msg' ,'Az új szabály létrehozása sikertelen!'),
				'errors'=>array()
			);
		}
		else{
			return array(
				'success'=>true,
				'error' => 0,
				'message'=>$end_message,
				'errors'=>array()
			);
		}
	}
	
	public function loadPriceRulesSablon($prs_id, $prs_raw_value, $campaign_id){
	
		if($prs_id == $prs_raw_value){
			return array(
				'success'=>false,
				'error' => 1,
				'message'=>Yii::t('msg' ,'Válassz sablont a listából!'),
				'errors'=>array()
			);
		}
		else{
			$cprlft1 = CampaignPriceRules::model()->find('campaign_id=:cid AND lft=1', array(':cid' => $campaign_id));
			$cprlft1_name = $cprlft1->name;
			
			CampaignPriceRules::model()->deleteAll('campaign_id=:cid', array(':cid' => $campaign_id));
			
			$sql = "INSERT INTO campaign_price_rules (campaign_id, lft, rgt, level, name, price, percent,link_id,link_type,price_type)
				  SELECT ".$campaign_id.", lft, rgt, level, name, price, percent,link_id,link_type,price_type
				  FROM price_rules_sablon_detail
			      WHERE price_rules_sablon_id =". $prs_id;

			if(!Yii::app()->db->createCommand($sql)->execute()){
				return array(
						'success'=>false,
						'error' => 1,
						'message'=>Yii::t('msg' ,'A szabály betöltése sikertelen!'),
						'errors'=>array()
				);
			}
			else{
				$cprlft1new = CampaignPriceRules::model()->find('campaign_id=:cid AND lft=1', array(':cid' => $campaign_id));
				$cprlft1new->name = $cprlft1_name;
				$cprlft1new->saveNode();
				
				return array(
						'success'=>true,
						'error' => 0,
						'message'=> 'A szabály betöltve!',
						'errors'=>array()
				);
			}
			
		}
	
		
	}
}

?>