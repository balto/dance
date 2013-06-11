<?php
class CampaignTypeManager extends BaseModelManager
{
    private static $instance = null;

    private function __construct() {
		
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new CampaignTypeManager();
        }
        return self::$instance;
    }

    public function delete($id) {
    	$errors = array();
    	
    	$ctds = CampaignTypeDetail::model()->findAll('campaign_type_id=:ctid', array(':ctid' => $id));
    	
    	$ctdIds = array();
    	
    	foreach ($ctds as $ctd) {
    		$ctdIds[] = $ctd->id;
    	}
    	
    	
    	if(Campaign::model()->count('campaign_type_detail_id IN ('.implode(',', $ctdIds).')')){
    		$errors[] = 'Addig nem törölhető amíg tartozik alá kampány!';
    	}
    	 
    	if (!empty($errors)) {
    		return array(
    				'success'=>true,
    				'error' => 1,
    				'message'=>Yii::t('msg' ,'Kampány típus törlése sikertelen!'),
    				'errors'=>$errors
    		);
    	}
    
    	$response_success_true = array(
    			'success'=>true,
    			'error' => 0,
    			'message'=>Yii::t('msg' ,'Kampány típus sikeresen törölve.')
    	);
    	$response_success_false = array(
    			'success'=>false,
    			'error' => 1,
    			'message'=>Yii::t('msg' ,'Kampány típus törlése sikertelen!'),
    			'errors'=>array()
    	);
    
    	$rows_deleted = CampaignType::model()->deleteByPk($id);
    
    	return $rows_deleted == 1 ? $response_success_true : $response_success_false;
    }
}

?>