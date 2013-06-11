<?php

class StatusManager extends BaseModelManager
{
    private static $instance = null;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new StatusManager();
        }
        return self::$instance;
    }

    public function getId($model, $column, $only_active = true){
// TODO: be kellene cache-elni a már egyszer elkkértet és onnan visszaadni
        $status = $this->getStatus($model, $column, $only_active);

        if($status['totalCount'] != 0){
            if($status['totalCount']==1){
                return $status['data'][0]['id'];
            }
            else{
                throw new Exception('A '.$model::model()->tableName().' táblában több darab '.$column.' = 1-es aktív sor van! ');
            }
        }
        else{
            throw new Exception('A '.$model::model()->tableName().' táblában nincs '.$column.' = 1-es aktív sor! ');
        }
    }

    public function getStatus($model, $column, $only_active = true){
        $query_params = array(
            array('select', 't.id, t.name'),
            array('from',$model::model()->tableName().' t'),
            array('order','name ASC'),
        );

        $where_str = 't.`'.$column.'` = 1';
        if ($only_active) $where_str .= ' AND t.`is_active` = 1';

        $query_params[] = array('where', array($where_str));

        return DBManager::getInstance()->query($query_params);
    }

}