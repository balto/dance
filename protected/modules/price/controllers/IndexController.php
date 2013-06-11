<?php

class IndexController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}
	
	public function actionShowPayIn()
	{
		$form = new PayInForm();
		//$form->bindActiveRecord(CampaignType::model());
	
		$this->render('/index/showPayIn.js', array(
				'form' => $form,
		));
	}
	
	public function actionGetPayInRecordData() {
		$id = $this->getParameter('id');
	
		$record = Ticket::model()->findByPk($id);
	
		$form_name = 'PayInForm';
		$form = new $form_name();
		//print_r($record); exit;
		$data = array(
			$form->generateName('id') => $record->id,
			$form->generateName('price') => $record->price - $record->payed_price,	
		);

		$this->renderText(json_encode(array('success'=>true, 'data'=>$data)));
	}
	
	public function actionSavePayIn()
	{
		
		$form = new PayInForm();
		$params = $this->getParameter($form->getNameFormat());

		$response = PriceManager::getInstance()->payIn($params['id'], $params['price']);

		
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