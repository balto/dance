<?php

class PermissionController extends Controller
{
    /**
     * A Felhasználó Jogosultságok listájának felületét állítja össze JS-ben
     *
     */
    public function actionIndex()
    {
        $this->render('/permission/list.js');
    }


    /**
     * Itt definiáljuk a Felhasználó Jogosultságok lista oszlopainak beállításait.
     * A konfiguráció az ExtJS GridColumn config paramétereinek felel meg.
     *
     * @return array
     */
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
                'name' => 'title',
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
                'flex' => 0.3,
        );

        $fields[] = array(
                'header' => Yii::t('msg','Technikai név'),
                'name' => 'name',
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
                'flex' => 0.3,
        );

        $fields[] = array(
                'header' => Yii::t('msg','Leírás'),
                'name' => 'description',
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
                'flex' => 0.4,
        );

        return $fields;
    }


    /**
     * A Felhasználó Jogosultságok listáját adja vissza JSON-ban.
     *
     */
    public function actionGetList() {

	    $user_id = $this->getParameter('user_id', null, false);

        $use_pager = !is_null($this->getParameter('start', null, false)) && !is_null($this->getParameter('limit', null, false));

	    $order = $this->getOrderParameters();

	    $attr = array('id',
	                  'name',
	                  'title',
	    			  'description',);

	    $params = array();
	    $params[] = array('select',implode(', ', $attr));
	    $params[] = array('from',Permission::model()->tableName().' p');

	    if ($user_id) {
	        $params[] = array('join', array('user_permission up', 'up.permission_id = p.id'));
	        $params[] = array('where', array('up.user_id = :user_id', array(':user_id' => $user_id)));
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

    public function actionReload() {

        UserManager::getInstance()->refreshAllPermissionsInDb();

        $this->renderText(json_encode(array('success' => true, 'message' => Yii::t('msg','Jogosultságok újratöltése sikeresen megtöntént'))));
    }

	/**
     * A Felhasználó Jogosultság adatlapot jeleníti meg, módosításhoz
     *
     */
    public function actionShow()
    {
        $form = new PermissionForm();

        $form->bindActiveRecord(Permission::model());

        $this->render('/permission/show.js', array(
			'form' => $form,
        ));
    }


    /**
     * A Felhasználó Jogosultság adatait gyűjti be és adja vissza JSON-ban
     *
     */
    public function actionFillForm()
    {
        $permission = Permission::model()->findByPk($this->getParameter('id'));

	    $form = new PermissionForm();

	    $data = array(
    	    $form->generateName('title')        => $permission->title,
    	    $form->generateName('name')         => $permission->name,
    	    $form->generateName('description')  => $permission->description,
	    );

	    $response = json_encode(array('success'=>true, 'data'=>$data));

	    $this->renderText($response);
    }
}