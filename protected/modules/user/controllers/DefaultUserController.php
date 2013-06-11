<?php

class DefaultUserController extends Controller
{

    /**
     * A Felhasználó Profil adatlapot jeleníti meg
     *
     */
	public function actionProfile()
	{
	    $form = new UserForm();
	    $profile_form = new UserProfileForm();

	    $form->bindActiveRecord(User::model());
	    $profile_form->bindActiveRecord(UserProfile::model());

	    $this->render('/defaultUser/profile.js', array(
	    	'form' => $form,
	    	'profile_form' => $profile_form,
	    ));
	}


    /**
     * A Felhasználó Profiljának adatait gyűjti be és adja vissza JSON-ban
     *
     */
	public function actionFillProfileForm()
	{
	    $user = User::model()->with('userProfile')->findByPk($this->getParameter('id'));

	    $form = new UserForm();
	    $profile_form = new UserProfileForm();

	    $data = array(
	        $form->generateName('id')            =>$user->id,
    	    $form->generateName('username')      =>$user->username,
    	    $form->generateName('loginname')     =>$user->loginname,
    	    $form->generateName('last_login')    =>$user->last_login,
            $form->generateName('created_at')    =>$user->created_at,
    	    $form->generateName('is_active')     =>$user->is_active,
    	    $form->generateName('is_super_admin')=>$user->is_super_admin,
    	    $profile_form->generateName('tel')   =>$user->userProfile->tel,
    	    $profile_form->generateName('mobil') =>$user->userProfile->mobil,
    	    $profile_form->generateName('email') =>$user->userProfile->email,
	    );

	    $response = json_encode(array('success'=>true, 'data'=>$data));
	    $this->renderText($response);

	}

    /**
     * A Felhasználó Profiljának adatait menti le
     *
     */
	public function actionSaveProfile()
	{
	    $errors = array();

	    $form = new UserForm('change_own');
	    $profile_form = new UserProfileForm();

	    $user_params = $this->getParameter($form->getNameFormat());
	    $profile_params = $this->getParameter($profile_form->getNameFormat());

	    $user = User::model()->findByPk($user_params['id']);

	    if ($user && $user_params['id'] == Yii::app()->user->id) {
	        $profile = $user->userProfile;

	        $form->bindActiveRecord($user);
	        $profile_form->bindActiveRecord($profile);

	        $form->bind($user_params);
	        $profile_form->bind($profile_params);

	        // form és ActiveRecord validalasok
	        $form->validate();
	        $profile_form->validate();

	        if ($form->hasErrors()) $errors = array_merge($errors, $form->getErrors());
	        if ($profile_form->hasErrors()) $errors = array_merge($errors, $profile_form->getErrors());

	        if (empty($errors)) {
	            $form->save();
	            $profile_form->save();
	        }

        } else {
            $errors[] = Yii::t('msg','Érvénytelen azonosító.');
        }

        if (empty($errors)) {
            $response = json_encode(array('success'=>true,
				'message'=>Yii::t('msg','A felhasználó adatai sikeresen rögzítve.'),
            ));
        } else {
            $response = json_encode(array(
                'success'=>false,
                'message'=>Yii::t('msg','A felhasználó adatainak módosítása nem sikerült az alábbi hibák miatt:'),
                'errors'=>Arrays::array_flatten($errors),
            ));
        }

        $this->renderText($response);

	}

	/**
	* Visszaadja a már az adott Felhasználóhoz rendelt Csoportok listáját
	*
	* használt ComponentManager-ek:
	* - User
	*
	* @param sfWebRequest $request
	* @return JSON
	*/
	public function actionGetAssociatedGroups() {
	    $user_id = $this->getParameter('id', Yii::app()->user->getId(), false);

	    $order = $this->getOrderParameters();

	    $pager = array();
	    $pager['limit'] = $this->getParameter('limit');
	    $pager['offset'] = $this->getParameter('start');

	    $groups = UserManager::getInstance()->getAssociatedGroups($user_id, $order, $pager);

	    $this->renderText(json_encode($groups));

	}


	/**
	* Visszaadja az adott Felhasználóhoz rendelt Összes Jogosultságot
	*
	* Örökölt jogosultságok (csoportokból) + egyéni!
	*
	* @return JSON
	*/
	public function actionGetAllPermissions() {
        $permissions = Yii::app()->user->getPermissions();

        $permissions = Arrays::arfsort($permissions, array(array('title','asc')));

        $response = array('totalCount'=>count($permissions), 'data'=>array_values($permissions));

        $this->renderText(json_encode($response));
	    //echo '{"totalCount":5,"data":[{"title":"Besorol\u00e1s SLA param\u00e9tereinek megtekint\u00e9se","description":"Megtekintheti egy Besorol\u00e1s SLA param\u00e9tereit"},{"title":"Besorol\u00e1s SLA param\u00e9tereinek m\u00f3dos\u00edt\u00e1sa","description":"M\u00f3dos\u00edthatja egy Besorol\u00e1s SLA param\u00e9tereit"},{"title":"Biztons\u00e1gi esem\u00e9nyek megtekint\u00e9se","description":"Megtekintheti a Biztons\u00e1gi esem\u00e9nyek adatait"},{"title":"Csak a felhaszn\u00e1l\u00f3 \u00e1ltal r\u00f6gz\u00edtett hibajegyek megtekint\u00e9se","description":"A felhaszn\u00e1l\u00f3 csak az \u00e1ltala r\u00f6gz\u00edtett hibajegyeket l\u00e1thatja"},{"title":"Elj\u00e1r\u00e1srend lista","description":"Megtekintheti az elj\u00e1r\u00e1srendek list\u00e1j\u00e1t"}]}';
	}

    /**
     * Itt definiáljuk a Felhasználó csoportjait tartalmazó lista oszlopainak beállításait.
     * A konfiguráció az ExtJS GridColumn config paramétereinek felel meg.
     *
     * @return array
     */
	public function listAssociatedGroupsFieldDefinitions()
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
            'values' => array(
	    ),
	    );

	    $fields[] = array(
            'header' => Yii::t('msg','Csoport'),
            'name' => 'name',
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
            'values' => array(
	    ),
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
            'values' => array(
	    ),
	    );

	    return $fields;
	}

    /**
     * Itt definiáljuk a Felhasználó egyéni jogait tartalmazó lista oszlopainak beállításait.
     * A konfiguráció az ExtJS GridColumn config paramétereinek felel meg.
     *
     * @return array
     */
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
            'values' => array(
	    ),
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
            'values' => array(
	    ),
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
            'values' => array(
	    ),
	    );

	    return $fields;
	}
}