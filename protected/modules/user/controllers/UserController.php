<?php

class UserController extends Controller
{
    public function actionIndex()
    {
        $this->render('/user/list.js', array(
            'max_per_page' => Yii::app()->params['extjs_pager_max_per_page']
        ));
    }

    /**
     * Itt definiáljuk a Felhasználók lista oszlopainak beállításait.
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
            'width' => 0,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => Yii::t('msg','Felhasználó neve'),
            'name' => 'username',
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
            'header' => Yii::t('msg','Bejelentkezési azonosító'),
            'name' => 'loginname',
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
            'width' => 50,
            'flex' => 1,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => Yii::t('msg','Utolsó aktivitás'),
            'name' => 'last_activity',
            'mapping' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => 'ASC',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => 'center',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            'width' => 50,
            'flex' => 1,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => Yii::t('msg','Csoportjai'),
            'name' => 'groups',
            'mapping' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => true,
            'gridColumn' => false,
            'width' => 300,
            'flex' => 2,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => Yii::t('msg','Utolsó belépés'),
            'name' => 'last_login',
            'mapping' => '',
            'method' => '',
            'type' => 'date',
            'sortType' => '',
            'sortDir' => 'DESC',
            'format' => Yii::app()->params['extjs_datetime_sec_format'],
            'defaultValue' => '',
            'resizable' => '',
            'align' => 'center',
            //'renderer' => "return value?Ext.util.Format.date(value,'Y-m-d H:i:s'):'';",
            'groupable' => false,
            'gridColumn' => true,
            'width' => 60,
            'flex' => 1,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => Yii::t('msg','Aktív'),
            'name' => 'is_active',
            'mapping' => '',
            'method' => '',
            'type' => '',
            'sortType' => '',
            'sortDir' => 'DESC',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => 'center',
            'renderer' => "function(value){return '<div title=\''+(value==0?'".Yii::t('msg', "inaktív, kattintson az aktiváláshoz")."':'".Yii::t('msg', "aktív, kattintson az inaktíváláshoz")."')+'\' class=\'grid-action active-state icon-bulb' + (value==0?'-off':'') + '\'>&nbsp;</div>';}",
            'groupable' => false,
            'gridColumn' => true,
            'width' => 30,
            'flex' => 0.5,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => Yii::t('msg','SuperAdmin'),
            'name' => 'is_super_admin',
            'mapping' => '',
            'method' => '',
            'type' => '',
            'sortType' => '',
            'sortDir' => 'DESC',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => 'center',
            //'renderer' => "return '&nbsp;';",
            'renderer' => "function(value){return '<div title=\''+(value==0?'".Yii::t('msg', 'Felhasználó, kattintson, ha SuperAdminná akarja tenni')."':'".Yii::t('msg', 'SuperAdmin, kattintson, ha nem akarja, hogy SuperAdmin legyen')."')+'\' class=\'grid-action superadmin-state icon-rosette' + (value==0?'-off':'') + '\'>&nbsp;</div>';}",
            'groupable' => false,
            'gridColumn' => true,
            'width' => 50,
            'flex' => 0.5,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => Yii::t('msg','Létrehozva'),
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
            //'renderer' => "function(value){return value?Ext.util.Format.date(value,'Y-m-d'):'';}",
            'groupable' => false,
            'gridColumn' => true,
            'width' => 50,
            'flex' => 1,
            'values' => array(
            ),
        );

        return $fields;
    }

    public function actionGetList()
    {
        $order = $this->getOrderParameters();

        $pager = array();
        $pager['limit'] = $this->getParameter('limit');
        $pager['offset'] = $this->getParameter('start');

        $users = UserManager::getInstance()->getUsers($order, $pager);

        $this->renderText(json_encode($users));
    }

    public function actionToggleUserIsActive()
    {
        $user_id = $this->getParameter('user_id');

        $response = UserManager::getInstance()->toggleUserIsActive($user_id);
        $this->renderText(json_encode($response));
    }

    public function actionToggleUserIsSuperAdmin()
    {
        $user_id = $this->getParameter('user_id');

        $response = UserManager::getInstance()->toggleUserIsSuperAdmin($user_id);
        $this->renderText(json_encode($response));
    }

    public function actionGeneratePassword()
    {
        $user_id = $this->getParameter('user_id');

        $response = UserManager::getInstance()->generatePassword($user_id);
        $this->renderText(json_encode($response));
    }

    public function actionDelete()
    {
        $errors = array();

        $user_id = $this->getParameter('user_id');

        $user = User::model()->findByPk($user_id);

        if(!Yii::app()->user->getDbUser()->is_super_admin && $user->is_super_admin){
            $errors[] = Yii::t('msg','Superadmint nem törölhet!');
        }
        elseif(Yii::app()->user->getDbUser()->id == $user->id){
            $errors[] = Yii::t('msg','Saját magát nem törölheti!');
        }

        if (empty($errors)) {
            $response = UserManager::getInstance()->deleteUser($user_id);
        }
        else{
            $response = array(
                'success'=>false,
                'message'=>Yii::t('msg','A felhasználó törlése nem sikerült az alábbi hibák miatt:'),
                'errors'=>Arrays::array_flatten($errors),
            );
        }

        $this->renderText(json_encode($response));
    }

    public function actionShow()
    {
        $form = new UserForm();
        $profile_form = new UserProfileForm();

        $form->bindActiveRecord(User::model());
        $profile_form->bindActiveRecord(UserProfile::model());

        $this->render('/user/show.js', array(
            'form' => $form,
            'profile_form' => $profile_form,
        ));
    }

    public function actionSave()
    {
        $errors = array();
        $is_edit = false;

        $form = new UserForm();
        $profile_form = new UserProfileForm();


        //$not_visible_meters = $this->getParameter('not_visible_meters', array(), false);
        $user_params = $this->getParameter($form->getNameFormat());
        $profile_params = $this->getParameter($profile_form->getNameFormat());
        $groups_data = $this->getParameter('groups_list');
        $permissions_data = $this->getParameter('permissions_list');

        if ($user_params['id']) {
            $is_edit = true;
            $user = User::model()->findByPk($user_params['id']);
            $profile = $user->userProfile;
        } else {
            $user = new User();
            $profile = new UserProfile();
            $profile->user_id = -1;
        }

        if($is_edit){
            if(!Yii::app()->user->getDbUser()->is_super_admin && $user->is_super_admin){
                $errors[] = Yii::t('msg','Superadmint nem módosíthat!');
            }
        }

        if ($user) {
            /*** User data és Profile validálása ***/

            $form->bindActiveRecord($user);
            $profile_form->bindActiveRecord($profile);

            $form->bind($user_params);
            $profile_form->bind($profile_params);

            // form és ActiveRecord validalasok
            $form->validate();
            $profile_form->validate();

            if ($form->hasErrors()) $errors = array_merge($errors, $form->getErrors());
            if ($profile_form->hasErrors()) $errors = array_merge($errors, $profile_form->getErrors());

            /***** User Group validálása ******/

            $group_params = array();
            if (!empty($groups_data)) {
                $group_params = explode(',', $groups_data);

                $command = Yii::app()->db->createCommand();
                $command->select('count(*)')
                        ->from('user_group')
                        ->where(array('in', 'id', $group_params));

                $count = $command->queryScalar();

                if ($count != count($group_params)) $errors[] = Yii::t('msg','A jogosultsági csoportok listája hibás!');
            }


            /***** Permissions validálása *****/

            $permission_params = array();
            if (!empty($permissions_data)) {
                $permission_params = explode(',', $permissions_data);

                $command = Yii::app()->db->createCommand();
                $command->select('count(*)')
                        ->from('permission')
                        ->where(array('in', 'id', $permission_params));

                $count = $command->queryScalar();

                if ($count != count($permission_params)) $errors[] = Yii::t('msg','Az egyéni jogosultságok listája hibás!');
            }

            /**** MENTÉS ****/

            if (empty($errors)) {
                if (!$form->save()) throw new EException('User Save Failed: '. $user_params['username']);
                $user_id = $form->getActiveRecord()->id;
                $profile_params['user_id'] = $user_id;
                $user_params['id'] = $user_id;
                $profile_form->bind($profile_params);
                if (!$profile_form->save()) throw new EException('User Profile Save Failed: '. $user_params['username']);;

                // felesleges csoport-tagságokat eldobjuk
                $filter = '';
                if (count($group_params)) {
                    $filter = " AND user_group_id NOT IN (".implode(',', $group_params).")";
                }
                UserUserGroup::model()->deleteAll(
                    "user_id = :id $filter",
                    array(":id" => $user_params['id'])
                );

                // meglévőket frissítjük
                foreach ($group_params as $user_group_id) {
                    $ug = UserUserGroup::model()->find(
                        'user_id = :user_id AND user_group_id = :group_id',
                        array(
                            ':user_id' => $user_params['id'],
                            ':group_id' => $user_group_id,
                        )
                    );

                    if (is_null($ug)) {    // nincs ilyen record
                        $ug = new UserUserGroup();
                        $ug->user_id = $user_params['id'];
                        $ug->user_group_id = $user_group_id;
                        $ug->created_by = Yii::app()->user->id;
                    }

                    $ug->updated_by = Yii::app()->user->id;
                    if (!$ug->save()) {
											print_r($ug->getErrors());die();
											throw new EException('User Group Save Failed: '.$user_group_id);
										}
                }

                // felesleges permission-öket eldobjuk
                $filter = '';
                if (count($permission_params)) {
                    $filter = "AND permission_id NOT IN (".implode(',', $permission_params).")";
                }
                UserPermission::model()->deleteAll(
                    "user_id = :id $filter",
                    array(":id" => $user_params['id'])
                );

                // meglévőket frissítjük
                foreach ($permission_params as $permission_id) {
                    $up = UserPermission::model()->find(
                        'user_id = :user_id AND permission_id = :perm_id',
                        array(
                            ':user_id' => $user_params['id'],
                            ':perm_id' => $permission_id,
                        )
                    );

                    // újakat felvesszük
                    if (is_null($up)) {
                        // nincs ilyen record
                        $up = new UserPermission();
                        $up->user_id = $user_params['id'];
                        $up->permission_id = $permission_id;
                        $up->created_by = Yii::app()->user->id;
                    }

                    $up->updated_by = Yii::app()->user->id;
                    if (!$up->save()) {
                        //print_r($up->getErrors()); exit;
                        throw new EException('User Permissions Save Failed: '.$permission_id);
                    }
                }


                // visibility mentése
                /*$command = Yii::app()->db->createCommand();
                $command->bulkInsertClearAll(); // ürítjük

                $target_table_name = MeterVisibility::model()->tableName();

                MeterVisibility::model()->deleteAll('visibility_user_id=:user_id', array(':user_id' => $user->id));

                foreach ($not_visible_meters AS $not_visible_meter_id){
                    $data = array();
                    $data["visibility_user_id"] = $user->id;
                    $data["visibility_meter_id"] = $not_visible_meter_id;

                    $command->bulkInsertCollect($target_table_name, $data);
                }

                try {
                    if ($command->hasBulkInsertData($target_table_name)) {
                        $command->bulkInsertExecute($target_table_name);
                    }

                } catch (Exception $e) {
                    $errors[] = Yii::t('msg','Nem sikerült a láthatóságot elmenteni!');
                }*/
            }

        } else {
            $errors[] = Yii::t('msg','Érvénytelen felhasználó azonosító');
        }

        if (empty($errors)) {
            $response = json_encode(array(
                'success'=>true,
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

    public function actionGetCredentials()
    {
        // semmit sem kell csinálni, mert a Controller ősosztályban le van kezelve a
        //  credential-ök visszaadása a renderText felülírásával
        $this->renderText(json_encode(array()));
    }


    /*public function actionGetNotVisibilityMeters(){
        $params = array();

        $user_id = $this->getParameter('user_id', null, false);

        $params[] = array('select','m.id, m.identifier,m.name');
        $params[] = array('from',  MeterVisibility::model()->tableName());
        $params[] = array('join',array(Meter::model()->tableName().' m','m.id = '.MeterVisibility::model()->tableName().'.visibility_meter_id'));
        $params[] = array('where', array('visibility_user_id=:user_id', array(':user_id' => $user_id)));

        $this->handlePager($params);
        $this->handleOrder($params);
        $this->handleFilter($params);

        $results = DBManager::getInstance()->query($params);

        $this->renderText(json_encode($results));
    }*/

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
            'sortDir' => '',
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
            'sortDir' => 'asc',
            'format' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
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

    public function listVisibilityFieldDefinitions($suggest = false)
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
            'header' => Yii::t('msg','Azonosító'),
            'name' => 'identifier',
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
            'filter' => array(array("xtype" => "textfield",
                                   "filterName" => "identifier",
                                   "fieldLabel" => "Azonosító",
                                   "anyMatch" => true)),
        );

        $fields[] = array(
            'header' => Yii::t('msg','Név'),
            'name' => 'name',
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
            'gridColumn' => true,
            'filter' => array(array("xtype" => "textfield",
                                   "filterName" => "name",
                                   "fieldLabel" => "Név",
                                   "anyMatch" => true)),
            'flex' => 1,

        );
        if($suggest){
            $fields[] = array(
                'header' => Yii::t('msg','Név'),
                'name' => 'display_name',
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
                'gridColumn' => true,
                'flex' => 1,

            );
        }

        return $fields;
    }

}