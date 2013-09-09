<?php

class TicketApi extends SiteApi {

    protected static $instance;

    /**
     * Visszaadja az objektum peldanyat.
     *
     * @return
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    public function getTicketTypes($extra_params, $isCombo){
        return $this->callApi('/ticket/index/getTicketTypes', array_merge($extra_params, array('is_combo' => $isCombo)));
    }
}