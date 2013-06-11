<?php
class InterfaceManager extends BaseModelManager
{
    private static $instance = null;

    private function __construct() {

    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new InterfaceManager();
        }
        return self::$instance;
    }

    /**
     *  Megadott campany tipushoz adja vissza a berlet tipusokat
     * 
     * @param int $campaignTypeId
     * @param array $extra_params
     * @return array
     */
	public function getTicketTypesForCampaignTypes($campaignTypeId, array $extra_params = array())
    {
    	
        $query_params = array(
            array('select', 'ttct.ticket_type_id'),
            array('from', TicketTypeCampaignType::model()->tableName().' ttct'),
            //array('join', array(CampaignType::model()->tableName() . ' ct', 'ct.id = ttct.campaign_type_id')),
            //array('join', array(TicketType::model()->tableName() . ' tt', 'tt.id = ttct.ticket_type_id')),
        	array('where', array('ttct.is_main = 1 AND ttct.campaign_type_id=:campaign_type_id', array(':campaign_type_id' => $campaignTypeId))),
        );

        //$query_params = array_merge($query_params, $extra_params);

        $ticketTypes = DBManager::getInstance()->query($query_params);
        
        $result['totalCount'] = 0;
        $result['data'] = array();
        
        if($ticketTypes['totalCount'] == 0){
        	return $result;
        }
        
        $ticketTypeIds = array();
        
        foreach ($ticketTypes['data'] AS $value){
        	$ticketTypeIds[] = $value['ticket_type_id'];
        }
        
        $query_params = array(
        		array('select', 'tt.id, dt.name AS dance_type_name, ct.name AS campaign_type_name, tt.moment_count, tt.valid_days, ttct.is_main, ttct.is_free'),
        		array('from', TicketTypeCampaignType::model()->tableName().' ttct'),
        		array('join', array(CampaignType::model()->tableName() . ' ct', 'ct.id = ttct.campaign_type_id')),
        		array('join', array(TicketType::model()->tableName() . ' tt', 'tt.id = ttct.ticket_type_id')),
        		array('join', array(DanceType::model()->tableName() . ' dt', 'dt.id = ct.dance_type_id')),
        		array('where', array('ttct.ticket_type_id IN ('.implode(', ', $ticketTypeIds).')')),
        		);
        
        $ticketRows = DBManager::getInstance()->query($query_params);

        $tickets = array();
        
        foreach ($ticketRows['data'] AS $ticketRow){
        	$display_name = $ticketRow['campaign_type_name']." (".$ticketRow['dance_type_name'].") ". $ticketRow['moment_count']." alkalmas";
        	$tickets[$ticketRow['id']]['data'][] = array('display_name' => $display_name, 'is_main' => $ticketRow['is_main'], 'is_free' => $ticketRow['is_free']);
        	$tickets[$ticketRow['id']]['valid_days'] = $ticketRow['valid_days'];
        }
        
        $result['totalCount'] = count($tickets);
        
        foreach ($tickets as $ticket_id => $data) {
        	$forimplode = array();
        	foreach ($data['data'] as $value) {
				if($value['is_main']){
					$forimplode[] = $value['display_name'];
				}
			}
			
        	$result['data'][] = array('id' => $ticket_id, 'name' => implode(', ', $forimplode), 'valid_days' => $data['valid_days']);
        }
        
        return $result;
    }
    
    /**
     * Uj berlet felbvetelnel levo autocomplete eredmenyet adja
     * 
     * @param string $query
     * @param array $extra_params
     */
    public function getMembers($query, array $extra_params = array())
    {
    	$query_params = array(
    		array('select', 'm.id, CONCAT(name," ",email," ", birthdate," ", address) AS name'),
    		array('from', Member::model()->tableName().' m'),
    		array('where', array("m.name LIKE '%".$query."%' OR email LIKE '%".$query."%'")),
    	);
    
    	$query_params = array_merge($query_params, $extra_params);
    
    	return DBManager::getInstance()->query($query_params);
    }

}

?>