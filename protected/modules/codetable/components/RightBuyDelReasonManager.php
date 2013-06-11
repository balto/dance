<?php
class RightBuyDelReasonManager extends CodetableManager
{
    private static $instance = null;

    private function __construct() {

    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new RightBuyDelReasonManager();
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
            'sortDir' => 'ASC',
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
            'header' => 'Vásárlási jogcím',
            'name' => 'buy_only',
            'mapping' => '',
            'method' => '',
            'type' => '',
            'format' => '',
            'sortType' => '',
            'sortDir' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => 'center',
            'renderer' => "function(value){return (value == 1 ? 'igen' : 'nem');}",
            'groupable' => false,
            'gridColumn' => true,
            'width' => 50,
            'flex' => 1,
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
            'width' => 30,
            'flex' => 0.5,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Létrehozva',
            'name' => 'created_at',
            'mapping' => '',
            'method' => '',
            'type' => 'date',
            'format' => Yii::app()->params['extjs_datetime_sec_format'],
            'sortType' => '',
            'sortDir' => 'DESC',
            'defaultValue' => '',
            'resizable' => '',
            'align' => 'center',
            'renderer' => "function(value){return value?Ext.util.Format.date(value,'Y-m-d H:i'):'';}",
            'groupable' => false,
            'gridColumn' => true,
            'width' => 50,
            'flex' => 1,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Módosítva',
            'name' => 'updated_at',
            'mapping' => '',
            'method' => '',
            'type' => 'date',
            'format' => Yii::app()->params['extjs_datetime_sec_format'],
            'sortType' => '',
            'sortDir' => 'DESC',
            'defaultValue' => '',
            'resizable' => '',
            'align' => 'center',
            'renderer' => "function(value){return value?Ext.util.Format.date(value,'Y-m-d H:i'):'';}",
            'groupable' => false,
            'gridColumn' => true,
            'width' => 50,
            'flex' => 1,
            'values' => array(
            ),
        );

        return $fields;
    }

    protected function getSelectFields() {
        return array(
            'id', 'name', 'buy_only', 'is_active', 'UNIX_TIMESTAMP(created_at) as created_at', 'UNIX_TIMESTAMP(updated_at) as updated_at'
        );
    }

    public function getRecordData($model_name, $id) {
        $record = $model_name::model()->findByPk($id);

        return array(
        	'id' => $record->id,
        	'name' => $record->name,
        	'buy_only' => $record->buy_only,
        	'is_active' => $record->is_active,
        );
    }
}

?>