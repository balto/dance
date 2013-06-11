<?php
class CodetableManager extends BaseModelManager
{

    private static $instance = null;

    private function __construct() {

    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new CodetableManager();
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
            'sortDir' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => 'center',
            'renderer' => "function(value){return value?Ext.Date.format(new Date(value), 'Y-m-d H:i'):'';}",
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
            'sortDir' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => 'center',
            'renderer' => "function(value){return value?Ext.Date.format(new Date(value),'Y-m-d H:i'):'';}",
            'groupable' => false,
            'gridColumn' => true,
            'width' => 50,
            'flex' => 1,
            'values' => array(
            ),
        );

        return $fields;
    }

    public function getCodeRecords($model_name, $active_only = true, $extra_params = array()){
        $query_params = $this->getBaseQueryParams($model_name, $active_only);

        $query_params = array_merge($query_params, $extra_params);

        return DBManager::getInstance()->query($query_params);
    }

    protected function getBaseQueryParams($model_name, $active_only = true) {
        $table_name = $model_name::model()->tableName();

        $query_params = array(
            array('select', implode(',', $this->getSelectFields())),
            array('from',$table_name),
        );

        if ($active_only) {
            $query_params[] = array('where', 'is_active = 1');
        }

        return $query_params;
    }

    protected function getSelectFields() {
        return array(
            'id', 'name', 'is_active', 'created_at', 'updated_at'
        );
    }

    public function getRecordData($model_name, $id) {
        $record = $model_name::model()->findByPk($id);

        return array(
        	'id' => $record->id,
        	'name' => $record->name,
        	'is_active' => $record->is_active,
        );
    }

    public function toggleCodeRecordActive($model_name, $id)
    {
        $record = $model_name::model()->findByPk($id);
        $record->is_active = 1-$record->is_active;

        if ($record->save()) {
            $response = array('success'=>true, 'message'=>sprintf(($record->is_active ? '%s &bdquo;<b>%s</b>&rdquo; törzsadat aktiválva' : '%s &bdquo;<b>%s</b>&rdquo; törzsadat inaktiválva.'), ucfirst($this->plural($record->name)), $record->name));
        } else {
            $response = array('success'=>false, 'message'=>sprintf(($record->is_active ? '%s &bdquo;<b>%s</b>&rdquo; törzsadat aktiválása nem sikerült!' : '%s &bdquo;<b>%s</b>&rdquo; törzsadat inaktiválása nem sikerült!'), ucfirst($this->plural($record->name)), $record->name), 'errors' => ModelManager::getModelErrors($record));
        }

        return $response;
    }

    public function deleteCodeRecord($model_name, $id)
    {
        return $this->delete($model_name, $id, 'A törzsadat');
    }

    private function plural($text)
    {
        $vowels = array('a', 'á', 'e', 'é', 'i', 'í', 'o', 'ó', 'ö', 'ő', 'u', 'ú', 'ü', 'ű');

        if (in_array(mb_strtolower(mb_substr($text, 0, 1, 'utf-8'), 'utf-8'), $vowels)) {
            return 'az';
        } else {
            return 'a';
        }
    }
		
	public function getFormData($modelName, $id)
	{
		$formModel = BaseModelManager::getFormClass($modelName);
		$formModel->bindActiveRecord($modelName::model()->findByPk($id));
		
		$data = array();
		foreach ($formModel->getActiveRecord()->getAttributes() as $name => $value) {
			$data[$formModel->generateName($name)] = $value;
		}
		
		return $data;
	}
}