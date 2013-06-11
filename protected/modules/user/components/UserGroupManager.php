<?php
class UserGroupManager extends BaseModelManager
{
    private static $instance = null;

    private function __construct() {

    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new UserGroupManager();
        }
        return self::$instance;
    }

    public function getUserGroupPermissions($user_group_id = null, $pager = array(), $order = array()){
        $results = array();

        $select_fields = array(
    		'p.id',
        	'p.title',
        	'p.description',
        );

        $query_params = array(
            array('select', implode(',', $select_fields)),
            array('from',Permission::model()->tableName()." p"),

        );

        if (!is_null($user_group_id)){
            if (!is_array($user_group_id) && is_numeric($user_group_id)){
                $user_group_id = array($user_group_id);
            }

            $query_params[] = array('join',array(UserGroupPermission::model()->tableName()." ugp","ugp.".Permission::model()->tableName()."_id = p.id"));
            $query_params[] = array('where', array("ugp.".UserGroup::model()->tableName()."_id IN (".implode(", ", $user_group_id).")"));
        }

        if (!empty($pager)) {
            $query_params[] = array('limit', $pager['limit']);
            $query_params[] = array('offset', $pager['offset']);
        }

        if (!empty($order)){
            $query_params[] = array('order', array($order));
        }

        $results = DBManager::getInstance()->query($query_params);

        return $results;
    }

    public function getUserGroupPermissionsDiff($user_group_id) {
        return $this->dbmanagerDiff("id", $this->getUserGroupPermissions(), $this->getUserGroupPermissions($user_group_id));
    }

    private function dbmanagerDiff($key ,$db_manager_result1, $db_manager_result2){
        $datas1 = $db_manager_result1["data"];
        $datas2 = $db_manager_result2["data"];

        $diff_keys1 = array();
        $diff_keys2 = array();

        foreach ($datas1 as $data1) {
            $diff_keys1[] = $data1[$key];
        }

        foreach ($datas2 as $data2) {
            $diff_keys2[] = $data2[$key];
        }

        $diffed = array_diff($diff_keys1, $diff_keys2);

        $diffed_results = array();

        foreach ($datas1 as $data1) {
            if (in_array($data1[$key], $diffed)){
                $diffed_results[] = $data1;
            }
        }

        return array("totalCount" => count($diffed_results), "data" => $diffed_results);
    }
}

?>