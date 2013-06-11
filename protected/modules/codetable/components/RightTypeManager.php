<?php
class RightTypeManager extends CodetableManager
{
    private static $instance = null;

    private function __construct() {

    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new RightTypeManager();
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
            'header' => 'Turnus típus',
            'name' => 'turn_type_name',
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
            'flex' => 1.8,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Apartman mérete',
            'name' => 'apart_size_name',
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
            'flex' => 0.8,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Jogidőtartam',
            'name' => 'lifetime',
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
            'flex' => 0.8,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Aktív',
            'name' => 'rt_is_active',
            'mapping' => '',
            'method' => '',
            'type' => '',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => 'center',
            'renderer' => "function(value){return '<div title=\''+(value==0?'inaktív, kattintson az aktiváláshoz':'aktív, kattintson az inaktíváláshoz')+'\' class=\'grid-action active-state icon-bulb' + (value==0?'-off':'') + '\'>&nbsp;</div>';}",
            'groupable' => false,
            'gridColumn' => true,
            'width' => 30,
            'flex' => 0.5,
            'values' => array(
            ),
        );

        return $fields;
    }

    protected function getBaseQueryParams($model_name, $active_only = true) {
        $query_params = array(
            array('select', implode(',', $this->getSelectFields())),
            array('from','right_type rt'),
            array('join', array('turn_type tt', 'rt.turn_type_id = tt.id')),
            array('join', array('right_lifetime lt', 'rt.lifetime_id = lt.id')),
            array('join', array('apart_size as', 'rt.apart_size_id = as.id')),
        );

        if ($active_only) {
            $query_params[] = array('where', 'rt.is_active = 1');
        }

        return $query_params;
    }

    protected function getSelectFields() {
        return array(
        	'rt.id as id', 'rt.name as name', 'tt.name as turn_type_name', 'as.name as apart_size_name', 'lt.name as lifetime', 'rt.is_active as rt_is_active'
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