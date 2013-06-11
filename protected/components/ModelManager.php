<?php

class ModelManager
{
    private static $instance = null;

    private function __construct() {}

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new ModelManager();
        }
        return self::$instance;
    }

    public static function getModelErrors(CActiveRecord $model)
    {
        $errors = $model->getErrors();
        $messages = array();
        foreach ($errors as $field => $msgs) {
            $messages[] = array('field'=>$field, 'message'=>implode(', ', $msgs));
        }

        return $messages;
    }
}
