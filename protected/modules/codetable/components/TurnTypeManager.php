<?php
class TurnTypeManager extends CodetableManager
{
    private static $instance = null;

    private function __construct() {

    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new TurnTypeManager ();
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
            'header' => 'Apartman minősége',
            'name' => 'apart_category_name',
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
            'header' => 'Apartman állapota',
            'name' => 'apart_quality_name',
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
            'flex' => 1,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Éjszakák száma',
            'name' => 'nights',
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
            'flex' => 1,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Idő pörgetése',
            'name' => 'rotate_time_type_name',
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
            'flex' => 1,
            'values' => array(
            ),
        );

        $fields[] = array(
        	'header' => 'Turnus kezdőnapja',
            'name' => 'start_day',
            'mapping' => '',
            'method' => '',
            'type' => '',
            'format' => '',
            'sortType' => '',
            'sortDir' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => "function(value, metaData, record){
            	var str ='';
            	if (value && !isNaN(value)) str += 'Január '+value+'.';
            	var weekdays = [ 'vasárnap', 'hétfő', 'kedd', 'szerda', 'csütörtök', 'péntek', 'szombat'];
            	if (record.data.start_weekday && !isNaN(record.data.start_weekday)) str += weekdays[record.data.start_weekday];
            	return str;
    		}",
            'groupable' => false,
            'gridColumn' => true,
            'flex' => 1,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Turnus kezdőnapja',
            'name' => 'start_weekday',
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
            'gridColumn' => false,
            'flex' => 1,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Hely pörgetése',
            'name' => 'rotate_loc_type_name',
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
            'flex' => 1,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Értékesíthető',
            'name' => 'sell',
            'mapping' => '',
            'method' => '',
            'type' => '',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => 'center',
            'renderer' => "function(value){return value==1?'igen':'nem';}",
            'groupable' => false,
            'gridColumn' => true,
            'flex' => 0.5,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Aktív',
            'name' => 'is_active',
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
            'flex' => 0.5,
            'values' => array(
            ),
        );

        return $fields;
    }

    protected function getBaseQueryParams($model_name, $active_only = true) {
        $query_params = array(
            array('select', implode(',', $this->getSelectFields())),
            array('from','turn_type tt'),
            array('leftjoin', array('apart_category ac', 'tt.apart_category_id = ac.id')),
            array('leftjoin', array('apart_quality aq', 'tt.apart_quality_id = aq.id')),
            array('join', array('rotate_time_type rtt', 'tt.rotate_time_type_id = rtt.id')),
            array('join', array('time_grid tg', 'tt.time_grid_id = tg.id')),
            array('join', array('rotate_loc_type rlt', 'tt.rotate_loc_type_id = rlt.id')),
        );

        if ($active_only) {
            $query_params[] = array('where', 'rt.is_active = 1');
        }

        return $query_params;
    }

    protected function getSelectFields() {
        return array(
        	'tt.id as id', 'tt.name as name', 'ac.name as apart_category_name', 'aq.name as apart_quality_name', 'nights',
        	'rtt.name as rotate_time_type_name', 'tg.start_day as start_day', 'tg.start_weekday as start_weekday',
        	'rlt.name as rotate_loc_type_name', 'sell', 'tt.is_active as is_active'
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