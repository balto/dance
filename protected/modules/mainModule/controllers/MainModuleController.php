<?php

class MainModuleController extends Controller
{
	public function teacherListFieldDefinitions() {
		$fields = array();
	
		$fields[] = array(
				'name' => 'id',
				'type' => '',
				'header' => 'Azonosító',
				'xtype' => '',
				'sortDir' => '',
				'gridColumn' => false,
		);
	
		$fields[] = array(
			'header' => 'Név',
			'name' => 'username',
			'mapping' => '',
			'method' => '',
			'type' => 'string',
			'sortType' => '',
			'sortDir' => '',
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
		);
	
		return $fields;
	}
	
	public function actionGetTeacherList()
	{
		$response = MainModuleManager::getInstance()->getTeachers();
	
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