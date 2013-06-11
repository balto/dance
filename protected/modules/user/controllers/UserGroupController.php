<?php

class UserGroupController extends Controller
{
    public function actionIndex()
    {
			$this->render('/userGroup/list.js');
    }

    /*List Field Definitions*/

    public function listFieldDefinitions()
    {
        $fields = array();
        $fields[] = array(
            'header' => Yii::t('msg','Azonosító'),
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
            'gridColumnWidth' => 0,
        );

        $fields[] = array(
            'header' => Yii::t('msg','Megnevezés'),
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
        );

        $fields[] = array(
            'header' => Yii::t('msg','Leírás'),
            'name' => 'description',
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
        );

        return $fields;
    }

    public function listAllPermissionsFieldDefinitions()
    {
        $fields = array();

        $fields[] = array(
            'header' => '',
            'name' => 'id',
            'mapping' => '',
            'method' => '',
            'type' => '',
            'sortType' => '',
            'sortDir' => '',
            'format' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => false,
            'width' => 0,
            'values' => array(),
        );

        $fields[] = array(
            'header' => Yii::t('msg','Jogosultság'),
            'name' => 'title',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => 'asc',
            'format' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            'flex' => 1,
            'values' => array(),
        );

        $fields[] = array(
            'header' => '',
            'name' => 'description',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => '',
            'format' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => false,
            'width' => 0,
            'values' => array(),
        );

        return $fields;
    }

    public function listAssociatedPermissionsFieldDefinitions()
    {
        $fields = array();

        $fields[] = array(
            'header' => '',
            'name' => 'id',
            'mapping' => '',
            'method' => '',
            'type' => '',
            'sortType' => '',
            'sortDir' => '',
            'format' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => false,
            'width' => 0,
            'values' => array(),
        );

        $fields[] = array(
            'header' => Yii::t('msg','Jogosultság'),
            'name' => 'title',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => 'asc',
            'format' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            'flex' => 1,
            'values' => array(),
        );

        $fields[] = array(
            'header' => '',
            'name' => 'description',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => '',
            'format' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => false,
            'width' => 0,
            'values' => array(),
        );

        return $fields;
    }

    /*List Field Definitions END*/

    /*Views*/
	public function actionShow() {
		$form_data = new UserGroupDataForm();
		$form_data->bindActiveRecord(UserGroup::model());

		$this->render('/userGroup/show.js', array(
			"form" => $form_data
		));
	}

    /*Views END*/

    /*Store*/
    public function actionGetAvailablePermissions() {
        $results = array();
        $id = $this->getParameter("id", null, false);

        $use_pager = !is_null($this->getParameter('start', null, false)) && !is_null($this->getParameter('limit', null, false));

        $pager = array();
        if ($use_pager) {
            $pager['limit'] = $this->getParameter('limit');
            $pager['offset'] = $this->getParameter('start');
        }

        $order = $this->getOrderParameters();

        if (!is_null($id)){
            $results = UserGroupManager::getInstance()->getUserGroupPermissionsDiff($id);
        }
        else{
            $results = UserGroupManager::getInstance()->getUserGroupPermissions(null, $pager, $order);
        }
        $this->renderText(json_encode($results));
    }

    public function actionGetAssociatedPermissions() {
        $results = array();
        $results["data"] = array();
        $results["totalCount"] = 0;

        $user_group_id = $this->getParameter("id", null, false);

        if (!is_null($user_group_id)){
            $results = UserGroupManager::getInstance()->getUserGroupPermissions($user_group_id);
        }

        $this->renderText(json_encode($results));
    }
    /*Store END*/

    /*Actions*/

    public function actionFillForm() {
        $id = $this->getParameter("id");

        $user_group = UserGroup::model()->findByPk($id);

        $form = new UserGroupDataForm();

        $name = $form->generateName('name');
        $description = $form->generateName('description');

        $data = array(
            $name         => $user_group->name,
            $description  => $user_group->description,
        );

        $response = json_encode(array('success'=>true, 'data'=>$data));

        $this->renderText($response);
    }

    public function actionGetList() {

	    $user_id = $this->getParameter('user_id', null, false);

        $use_pager = !is_null($this->getParameter('start', null, false)) && !is_null($this->getParameter('limit', null, false));

	    $order = $this->getOrderParameters();

	    $attr = array('id',
	                  'name',
	    			  'description',);

	    $params = array();
	    $params[] = array('select',implode(', ', $attr));
	    $params[] = array('from',UserGroup::model()->tableName().' g');

	    if ($user_id) {
	        $params[] = array('join', array('user_user_group ug', 'ug.user_group_id = g.id'));
            $params[] = array('where', array('ug.user_id = :user_id', array(':user_id' => $user_id)));
	    }

	    if ($use_pager) {
	        $params[] = array('limit', $this->getParameter('limit'));
	        $params[] = array('offset', $this->getParameter('start'));
	    }

	    if (!empty($order)){
	        $params[] = array('order', array($order));
	    }

	    $results = DBManager::getInstance()->query($params);

	    $this->renderText(json_encode($results));
    }

    public function actionDelete() {
        $success = false;
        $message = "";
        $user_group_id = $this->getParameter("record_id");

        $connected_user_count = UserUserGroup::model()->count(UserGroup::model()->tableName()."_id =:id", array(":id"=>$user_group_id));

        if ($connected_user_count > 0){
           $message = Yii::t('msg',"Addig nem törölhető, amíg van alatta user!");
        }
        else{
            UserGroup::model()->deleteByPk($user_group_id);
            UserGroupPermission::model()->deleteAll(UserGroup::model()->tableName()."_id =:id", array(":id"=>$user_group_id));
            $success = true;
        }

        $this->renderText(json_encode(array("success" => $success, "message" => $message)));
    }

    public function actionSave(){
        $delete_old_permissions = false;
        $errors = array();
        $form = new UserGroupDataForm();
        $user_group_params = $this->getParameter($form->getNameFormat());
        $associated_permission_ids = explode(',',$this->getParameter("permissions_list", '', false));

        if (isset($user_group_params["id"]) && $user_group_params["id"]!=""){
            $AR = UserGroup::model()->findByPk($user_group_params["id"]);
            $delete_old_permissions = true;
            $user_group_id = $user_group_params["id"];
            unset($user_group_params["id"]);
        }
        else{
            $AR = new UserGroup();
        }

        $form->bindActiveRecord($AR);
        $form->bind($user_group_params);
        $form->validate();
				
        if ($form->hasErrors()) $errors = array_merge($errors, $form->getErrors());

        if (empty($errors)) {
            if($form->save()){

                if ($delete_old_permissions && !empty($associated_permission_ids)) {
                    UserGroupPermission::model()->deleteAll(UserGroup::model()->tableName()."_id = :id",
                                                            array(":id" => $user_group_id));
                }

                $command = Yii::app()->db->createCommand();
                $command->bulkInsertClearAll(); // ürítjük
                $target_table_name = UserGroupPermission::model()->tableName();

                foreach ($associated_permission_ids AS $associated_permission_id){
                    $data[UserGroup::model()->tableName()."_id"] = $form->id;
                    $data[Permission::model()->tableName()."_id"] = $associated_permission_id;
                    $data["created_by"] = Yii::app()->user->getId();
                    $data["created_at"] = sfDate::getInstance()->formatDbDateTime();

                    $command->bulkInsertCollect($target_table_name, $data);
                }

                if ($command->hasBulkInsertData($target_table_name)) {
                    $command->bulkInsertExecute($target_table_name);
                }
            }
        }

        if (empty($errors)) {
            $response = json_encode(array('success'=>true,
        				'message'=>Yii::t('msg','A felhaszánló adatai sikeresen rögzítve.'),
            ));
        } else {
            $response = json_encode(array(
                        'success'=>false,
                        'message'=>Yii::t('msg','A felhaszánló adatainak módosítása nem sikerült az alábbi hibák miatt:'),
                        'errors'=>Arrays::array_flatten($errors),
            ));
        }

        $this->renderText($response);
    }

    /*Actions END*/
}