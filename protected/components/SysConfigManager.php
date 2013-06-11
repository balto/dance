<?php

class SysConfigManager extends BaseModelManager
{
    private static $instance = null;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new SysConfigManager();
        }
        return self::$instance;
    }

    public function __get($name){
// TODO: be kellene cache-elni a már egyszer elkértet és onnan visszaadni

        $field_name = sfInflector::underscore($name);

        return SystemConfig::model()->findByPk(1)->$field_name;
    }

}