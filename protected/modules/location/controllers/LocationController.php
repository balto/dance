<?php

class LocationController extends Controller
{
public function listFieldDefinitions()
	{
		$ext = extjsHelper::ext_getHelper();
	
		$fields = array();
	
		$labels = Location::model()->attributeLabels();
		
		$fields[] = array(
			'header' => 'Id',
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
			'width' => 1,
			'values' => array(
			),
		);
		
		$fields[] = array(
			'header' => $labels['name'],
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
			'flex' => 1,
			'values' => array(
			),
			'filter' => array(array("xtype" => "textfield",
					'filterName' => "l.name",
			)),
		);
	
		$fields[] = array(
			'header' => $labels['address'],
			'name' => 'address',
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
			'flex' => 1,
			'values' => array(
			),
			'filter' => array(array("xtype" => "textfield",
					'filterName' => "l.address",
			)),
		);
	
		
		return $fields;
	}
	
	
	public function actionIndex()
	{
		$combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];

        $this->render('/location/list.js', array(
            'max_per_page' => Yii::app()->params['extjs_pager_max_per_page'],
            'combo_max_per_page' => $combo_max_per_page,
        ));
	}
	
	public function actionGetList()
	{
		$extra_params = array();
		$this->addPagerParams($extra_params);
		$this->addOrderParams($extra_params);
		$this->addFilterParams($extra_params);
		
		$response = LocationManager::getInstance()->getLocation($extra_params);
	
		$this->renderText(json_encode($response));
	}
	
	public function actionShow()
	{
		$combo_max_per_page = Yii::app()->params['extjs_combo_pager_max_per_page'];
		$max_per_page = Yii::app()->params['extjs_pager_max_per_page'];
	
		$form = new LocationForm();
		$form->bindActiveRecord(Location::model());
	
		$this->render('/location/show.js', array(
			'form' => $form,
			'max_per_page' => $max_per_page,
			'combo_max_per_page' => $combo_max_per_page,
		));
	}
	
	public function actionGetRecordData() {
		$id = $this->getParameter('id');
	
		$record = Location::model()->findByPk($id);
	
		$form_name = 'LocationForm';
		$form = new $form_name();
		//print_r($record); exit;
		$data = array(
			$form->generateName('id') => $record->id,
			$form->generateName('name') => $record->name,
			$form->generateName('address') => $record->address,
			$form->generateName('is_active') => (int)$record->is_active,
		);
	
		$this->renderText(json_encode(array('success'=>true, 'data'=>$data)));
	}
	
	public function actionSave()
	{
		$form = new LocationForm();
		$params = $this->getParameter($form->getNameFormat());
	
		$response = LocationManager::getInstance()->save('Location', $params, $form->getNameFormat().'Form');
	
		$this->renderText(json_encode($response));
	}
	
	public function actionDeleteLocation(){
		$id = $this->getParameter('id');
	
		$response = LocationManager::getInstance()->delete($id);
	
		$this->renderText(json_encode($response));
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}