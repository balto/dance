<?php
class HotelManager extends CodetableManager
{
    private static $instance = null;

    private function __construct() {

    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new HotelManager();
        }
        return self::$instance;
    }

    /**
     * Itt definiáljuk a törzsadat modelek lista oszlopainak beállításait.
     * A konfiguráció az ExtJS GridColumn config paramétereinek felel meg.
     *
     * @return array
     */
    public function listFieldDefinitions()
    {
        $fields = array();
        $fields[] = array(
            'header' =>'Azonosító',
            'name' => 'id',
            'mapping' => '',
            'method' => '',
            'type' => '',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => 'right',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => false,
            'width' => 0,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Megnevezés',
            'name' => 'name',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => 'ASC',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            'width' => 150,
            'flex' => 3,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Rövid megnevezés',
            'name' => 'short_name',
            'mapping' => '',
            'method' => '',
            'type' => '',
            'format' => '',
            'sortType' => '',
            'sortDir' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            //'width' => 50,
            'flex' => 1,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Telefon',
            'name' => 'phone',
            'mapping' => '',
            'method' => '',
            'type' => '',
            'format' => '',
            'sortType' => '',
            'sortDir' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            //'width' => 50,
            'flex' => 1,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Email',
            'name' => 'email',
            'mapping' => '',
            'method' => '',
            'type' => '',
            'format' => '',
            'sortType' => '',
            'sortDir' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            'flex' => 1.5,
            'values' => array(
            ),
        );

        return $fields;
    }

    protected function getBaseQueryParams($model_name, $active_only = true) {
        $query_params = array(
            array('select', implode(',', $this->getSelectFields())),
            array('from','hotel'),
        );

        /*if ($active_only) {
            $query_params[] = array('where', 'rt.active = 1');
        }*/

        return $query_params;
    }

    protected function getSelectFields() {
        return array(
        	'id', 'name', 'short_name', 'phone', 'email '
        );
    }

    /*public function getRecordData($model_name, $id) {
        $record = $model_name::model()->findByPk($id);

        return array(
        	'id' => $record->id,
        	'name' => $record->name,
        	'owner' => $record->owner,
        	'active' => $record->active,
        );
    }*/

}

?>