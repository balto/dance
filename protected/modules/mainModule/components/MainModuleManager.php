<?php
class MainModuleManager extends BaseModelManager
{
    private static $instance = null;

    private function __construct() {
		
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new MainModuleManager();
        }
        return self::$instance;
    }
    
    public function getTeachers()
    {
    	$query_params = array(
    			array('select', 'id, username'),
    			array('from', User::model()->tableName().' u'),
    			array('leftJoin', array(UserUserGroup::model()->tableName().' uug', 'uug.user_id = u.id')),
    			array('where', array('uug.user_group_id=:user_group_id AND u.is_active = 1', array(':user_group_id' => Yii::app()->params['teacher_user_group_id']))),
    	);
    
    	return DBManager::getInstance()->query($query_params);
    }
    
}