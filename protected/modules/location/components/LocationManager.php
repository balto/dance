<?php
class LocationManager extends BaseModelManager
{
    private static $instance = null;

    private function __construct() {

    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new LocationManager();
        }
        return self::$instance;
    }

    public function getLocation($extra_params = array())
    {
        $query_params = array(
            array('select', 'l.id, l.name, l.address, l.is_active'),
            array('from', Location::model()->tableName().' l'),
        );

        $query_params = array_merge($query_params, $extra_params);

        return DBManager::getInstance()->query($query_params);
    }
    
    public function delete($id) {
    	$errors = array();
    	 
    	if(Campaign::model()->count('location_id =:lid', array(':lid' => $id))){
    		$errors[] = 'Addig nem törölhető amíg tartozik alá kampány!';
    	}
    
    	if (!empty($errors)) {
    		return array(
    				'success'=>true,
    				'error' => 1,
    				'message'=>Yii::t('msg' ,'Hely törlése sikertelen!'),
    				'errors'=>$errors
    		);
    	}
    
    	$response_success_true = array(
    			'success'=>true,
    			'error' => 0,
    			'message'=>Yii::t('msg' ,'Hely sikeresen törölve.')
    	);
    	$response_success_false = array(
    			'success'=>false,
    			'error' => 1,
    			'message'=>Yii::t('msg' ,'Hely törlése sikertelen!'),
    			'errors'=>array()
    	);
    
    	$rows_deleted = Location::model()->deleteByPk($id);
    
    	return $rows_deleted == 1 ? $response_success_true : $response_success_false;
    }

}

?>