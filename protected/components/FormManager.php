<?php

class FormManager
{
    private static $instance = null;

    private function __construct() {}

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class();
        }
        return self::$instance;
    }

    public static function getFormErrors(CFormModel $form)
    {
        $errors = $form->getErrors();
        $messages = array();
        foreach ($errors as $field => $msgs) {
            $messages[] = array('field'=>$field, 'message'=>implode(', ', $msgs));
        }

        return $messages;
    }
}
