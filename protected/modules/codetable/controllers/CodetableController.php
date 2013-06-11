<?php

class CodetableController extends Controller
{
    public function actionIndex()
    {
        $model_name = $this->getParameter('model_name', null, false);
        $model_label = $this->getParameter('model_label', null, false);

        $manager = BaseModelManager::getManagerClass($model_name);

        $fields = $manager->listFieldDefinitions();
				
        $this->render('/codetable/list.js', array(
            'model_name' => $model_name,
            'model_label' => $model_label,
            'fields' => $fields,
            'max_per_page' => Yii::app()->params['extjs_pager_max_per_page']
        ));
    }

    public function actionGetList()
    {
        $model_name = $this->getParameter('model_name');
        $active_only = $this->getParameter('active_only', true, false);
        $where = json_decode($this->getParameter('where', null, false), true);
				
        $extra_params = array();
				if (is_array($where)) {
					$sql_where = "";
					$i = 0;
					foreach ($where as $field => $value) {
						$sql_where .= " {$field} = {$value}";
						if ($i<count($where)-1) {
							$sql_where .= " AND ";
						}
						$i++;
					}
					$extra_params[] = array("where", $sql_where);
					
				}
				
        $this->addPagerParams($extra_params);
        $this->addOrderParams($extra_params);
        $this->addFilterParams($extra_params);				

        $manager = BaseModelManager::getManagerClass($model_name);

        $response = $manager->getCodeRecords($model_name, $active_only, $extra_params);

        $this->handleFirstEmpty($response['data']);

        $this->renderText(json_encode($response));
    }

    public function actionToggleCodeRecordActive()
    {
        $model_name = $this->getParameter('model_name');
        $id = $this->getParameter('id');

        $response = CodetableManager::getInstance()->toggleCodeRecordActive($model_name, $id);

        $this->renderText(json_encode($response));
    }

    public function actionDelete()
    {
        $model_name = $this->getParameter('model_name');
        $id = $this->getParameter('id');

        $response =  CodetableManager::getInstance()->deleteCodeRecord($model_name, $id);

        $this->renderText(json_encode($response));
    }

    public function actionShow()
    {
        $modelName = $this->getParameter('model_name');
								
        $formModel = BaseModelManager::getFormClass($modelName);
        $formModel->bindActiveRecord($modelName::model());
								
        $templateName = (get_class($formModel)=='CodetableForm') ? 'show.js' : 'show'.$modelName.'.js';

        $this->render('/codetable/'.$templateName, array(
            'form' => $formModel,
						'model_label' => $this->getParameter('model_label', null, false),
            'max_per_page' => Yii::app()->params['extjs_pager_max_per_page'],
            'combo_max_per_page' => Yii::app()->params['extjs_combo_pager_max_per_page'],
        ));
    }

    public function actionGetRecordData() {
        $model_name = $this->getParameter('model_name');
        $id = $this->getParameter('id');

        $data = CodetableManager::getInstance()->getFormData($model_name, $id);

        $this->renderText(json_encode(array('success'=>true, 'data'=>$data)));								
    }


    public function actionSave()
    {
        $model_name = $this->getParameter('model_name');

        $form = BaseModelManager::getFormClass($model_name);
        $params = $this->getParameter($form->getNameFormat());

        $response = CodetableManager::getInstance()->save($model_name, $params, $form->getNameFormat().'Form');

        $this->renderText(json_encode($response));
    }
		
	public function getComboListData()
	{
		$model = $this->getParameter('model');
		
		$results = parent::getComboListData();
		
		if ($model == "TimeGrid") {
			$weekdays = array("vasárnap", "hétfő", "kedd", "szerda", "csütörtök", "péntek", "szombat");
			foreach ($results["data"] as $i => $data) {
				$results["data"][$i]["name"] = $data["nights"]." éjszakás";
				if ($data["start_day"]) $results["data"][$i]["name"] .= ", január ".$data["start_day"]."-i kezdéssel";
				elseif ($data["start_weekday"]) $results["data"][$i]["name"] .= ", ".$weekdays[max(6, $data["start_weekday"])]."i kezdéssel";
			}
		}
		elseif ($model == "RotateLocTable") {
			$locations = Yii::app()->db->createCommand()
				->select("t.id, group_concat(h.short_name order by r.year separator '->') name")
				->from("rotate_loc_table t")
				->join("rotate_loc_row r", "r.rotate_loc_table_id=t.id")
				->join("hotel h", "h.id=r.hotel_id")
				->group("t.id")
				->queryAll()
			;
			
			$results = array("data" => $locations);
		}
				
		return $results;
	}
}