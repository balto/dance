<?php

class UserManager extends BaseModelManager
{
    private static $instance = null;

	public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new UserManager();
        }
        return self::$instance;
    }

    public function getAssociatedGroups($user_id, $order=array(), $pager = array(), $index_by = null) {
        $query_params = array(
            array('select', 'g.id, g.name, g.description'),
            array('from','user_group g'),
            array('join', array('user_user_group ug', 'ug.user_group_id = g.id')),
            array('where', array('ug.user_id = :user_id', array(':user_id' => $user_id))),
        );

        if (!empty($order)) $query_params[] = array('order', array($order));

        if (!empty($pager)) {
            $query_params[] = array('limit', $pager['limit']);
            $query_params[] = array('offset', $pager['offset']);
        }

        return DBManager::getInstance()->query($query_params, !empty($pager), $index_by);
    }

    /**
     *
     * Visszaadja az adott felhasználónak a csoportokon keresztül megkapott jogait
     * @param int $user_id
     * @param array $order
     * @param array $pager
     */
    public function getUserGroupPermissions($user_id, $order=array(), $pager = array(), $index_by = null) {
        $query_params = array(
            array('select', 'p.id, p.name, p.title, p.description'),
            array('from','permission p'),
            array('join', array('user_group_permission ugp', 'ugp.permission_id = p.id')),
            array('join', array('user_user_group ug', 'ugp.user_group_id = ug.user_group_id')),
            array('where', array('ug.user_id = :user_id', array(':user_id' => $user_id))),
        );

        if (!empty($order)) $query_params[] = array('order', array($order));

        if (!empty($pager)) {
            $query_params[] = array('limit', $pager['limit']);
            $query_params[] = array('offset', $pager['offset']);
        }

        return DBManager::getInstance()->query($query_params, !empty($pager), $index_by);
    }

    /**
     *
     * Visszaadja az adott felhasználónak a saját jogait
     * @param int $user_id
     * @param array $order
     * @param array $pager
     */
    public function getUserOwnPermissions($user_id, $order=array(), $pager = array(), $index_by = null) {
        $query_params = array(
            array('select', 'p.id, p.name, p.title, p.description'),
            array('from','permission p'),
            array('join', array('user_permission up', 'up.permission_id = p.id')),
            array('where', array('up.user_id = :user_id', array(':user_id' => $user_id))),
        );

        if (!empty($order)) $query_params[] = array('order', array($order));

        if (!empty($pager)) {
            $query_params[] = array('limit', $pager['limit']);
            $query_params[] = array('offset', $pager['offset']);
        }

        return DBManager::getInstance()->query($query_params, !empty($pager), $index_by);
    }

    public function refreshAllPermissionsInDb() {
        $all_credentials = array();
        foreach(Yii::app()->modules as $module_name => $module_config) {
            if ($module_name != 'gii') {
                $module_class = sfInflector::camelize($module_name).'Module';

                Yii::import('application.modules.'.$module_name.'.'.$module_class, true);

                if (is_callable($module_class.'::getCredentials')) {
                    $module_credentials = $module_class::getCredentials();
                    $all_credentials = $all_credentials + $module_credentials;
                }
            }
        }

        $sql = 'SELECT id, name FROM permission';
        $permissions_in_db = Yii::app()->db->createCommand($sql)->queryAll();

        $existing_permissions = array();
        $delete_permissions = array();
        foreach($permissions_in_db as $permission_in_db){
            $credential_name = $permission_in_db['name'];
            if (array_key_exists($credential_name, $all_credentials)){
                $existing_permissions[$credential_name] = $permission_in_db;
            } else {
                $delete_permissions[] = $credential_name;
            }
        }

        if (!empty($delete_permissions)) {
            $command = Yii::app()->db->createCommand()
                ->delete('permission', array('in', 'name', $delete_permissions));
        }

        foreach ($all_credentials as $credential_name => $credential) {
            if (array_key_exists($credential_name, $existing_permissions)) {
                $permission_id = $existing_permissions[$credential_name]['id'];
                $command = Yii::app()->db->createCommand()
                    ->update('permission',
                        array(
                    		'name'=>$credential_name,
                    		'title'=>$credential['title'],
                    		'description'=>$credential['description'],
                    		'updated_by'=>Yii::app()->user->getId(),
                    		'updated_at'=>sfDate::getInstance()->formatTimeZoneDbDateTime(),
                        ),
                        'id=:id', array(':id'=>$permission_id)
                    );
            } else {
                $command = Yii::app()->db->createCommand()
                    ->insert('permission',
                        array(
                    		'name'=>$credential_name,
                    		'title'=>$credential['title'],
                    		'description'=>$credential['description'],
                    		'created_by'=>Yii::app()->user->getId(),
                    		'created_at'=>sfDate::getInstance()->formatTimeZoneDbDateTime(),
                    		'updated_by'=>Yii::app()->user->getId(),
                    		'updated_at'=>sfDate::getInstance()->formatTimeZoneDbDateTime(),
                         )
                    );
            }
        }
    }

    public function getUsers($order=array(), $pager = array())
    {
        $query_params = array(
            array('select', 'u.id, u.username, u.loginname, s.sess_time as last_activity, COALESCE(GROUP_CONCAT(g.name SEPARATOR ", "), "'.Yii::t('msg', '...csoport nélkül').'") as groups, UNIX_TIMESTAMP(u.last_login) last_login, u.is_active, u.is_super_admin, UNIX_TIMESTAMP(u.created_at) created_at'),
            array('from','user u'),
            array('leftJoin', array('sessions s', 's.sess_id = u.session_id AND s.sess_data != ""')),
            array('leftJoin', array('user_user_group ug', 'ug.user_id = u.id')),
            array('leftJoin', array('user_group g', 'g.id = ug.user_group_id')),
            array('group', array('u.id')),
        );

        if (!empty($order)) $query_params[] = array('order', array($order));

        if (!empty($pager)) {
            $query_params[] = array('limit', $pager['limit']);
            $query_params[] = array('offset', $pager['offset']);
        }

        $result = DBManager::getInstance()->query($query_params, !empty($pager));

        foreach ($result['data'] as &$row) {

            if ($row['id'] == Yii::app()->user->getId()) {
                $row['last_activity'] = Yii::t('msg' ,'most');
            } elseif (isset($row['last_activity'])) {
                $row['last_activity'] = Dates::dateDiffToHumanReadable($row['last_activity']-Yii::app()->session->getTimeout()) . ($row['last_activity'] < sfDate::getInstance()->get() ? Yii::t('msg' ,' <i>(lejárt)</i>') : '');
            }

        }

        return $result;
    }

    public function toggleUserIsActive($id)
    {
        $user = User::model()->findByPk($id);
        $user->is_active = 1-$user->is_active;
        // aktiváláskor a sikertelen próbálkozásait kitöröljük
        if ($user->is_active) $user->failed_logins = 0;
        if ($user->save()) {
            $response = array('success'=>true, 'message'=>sprintf(($user->is_active?Yii::t('msg' ,'<b>%s</b> felhasználó aktiválva'):Yii::t('msg', '<b>%s</b> felhasználó inaktiválva.')), $user->username));
        } else {
            $response = array('success'=>false, 'message'=>sprintf(($user->is_active?Yii::t('msg' ,'<b>%s</b> felhasználó aktiválása nem sikerült!'):Yii::t('msg' ,'<b>%s</b> felhasználó inaktiválása nem sikerült!')), $user->username), 'errors' => ModelManager::getModelErrors($user));
        }

        // ha most aktiváltunk egy olyan user-t, akinek nincs jelszava (mert sok sikertelen bejelentkezés miatt kitiltottuk)
        // akkor generálunk neki új jelszót.
        if ($response['success'] && $user->is_active && is_null($user->password)) {
            $gen_response = $this->generatePassword($id);
            if ($gen_response['success']) $response['message'] .= Yii::t('msg' ,'<br />A felhasználó új kezdeti jelszót kapott, amelyről emailben kap értesítést.');
        }

        return $response;
    }

    public function toggleUserIsSuperAdmin($id)
    {
        $session_user = Yii::app()->user->getDbUser();
        if ($session_user && !$session_user->is_super_admin) {
            return array('success'=>false, 'message'=>Yii::t('msg' ,'Hozzáférés megtagadva'), 'errors'=>array(Yii::t('msg' ,'Ön nem rendelkezik megfelelő jogosultsággal a művelet elvégzéséhez!')));
        } else {
            $user = User::model()->findByPk($id);
            $user->is_super_admin = 1 - $user->is_super_admin;
            if ($user->save()) {
                return array('success'=>true, 'message'=>sprintf(($user->is_super_admin?Yii::t('msg' ,'<b>%s</b> felhasználó SuperAdmin.'):Yii::t('msg' ,'<b>%s</b> felhasználó már nem SuperAdmin.')), $user->username));
            } else {
                return array('success'=>false, 'message'=>sprintf(($user->is_super_admin?Yii::t('msg' ,'<b>%s</b> felhasználó SuperAdmin státuszának beállítása nem sikerült!'):Yii::t('msg' ,'<b>%s</b> felhasználó SuperAdmin státuszának törlése nem sikerült!')), $user->username), 'errors' => ModelManager::getModelErrors($user));
            }
        }
    }

    /**
     * A Felhasználó számára új kezdeti jelszót generál és kiértesíti a felhasználót
     *
     * @param integer $id
     * @return array
     */
    public function generatePassword($id) {
        $user = User::model()->findByPk($id);

        // új jelszó lementése
        $pwgen = new PWGen();
        $new_password = $pwgen->generate();
        Yii::app()->user->setUserPasswordFields($user, $new_password);

        $user->is_first_password = 1;
        $user->is_active = 1;
        $user->failed_logins = 0;

        if (!$user->save()) {
            return array('success'=>false, 'message'=>sprintf(Yii::t('msg' ,'<b>%s</b> felhasználó számára az új jelszó generálása nem sikerült!'), $user->username), 'errors' => ModelManager::getModelErrors($user));
        }

        // kiértesítés a jelszó megváltozásáról
        $message = Yii::t('msg' ,'Jelszó generálás sikeresen megtörtént.');
        $config_email = Yii::app()->params['failed_logins']['generate_password']['email'];
        $subject = $config_email['subject-template'];
        $email_msg = sprintf($config_email['message-template'], $user->username, $user->loginname, Yii::app()->params['customer_name'], $new_password);

        $mailer_defaults = Yii::app()->params['email'];
        $email_msg .= $mailer_defaults['message-footer'];

        $recipients = array($user->userProfile->email => $user->username);

        $sender = array($mailer_defaults['senderEmail'] => $mailer_defaults['senderName']);
        $success = Mail::sendPlainTextMail($sender, $recipients, $subject, $email_msg);

        if ($success) {
            $message .= Yii::t('msg' ,'<br />Az új kezdeti jelszóról a felhasználó emailben kapott értesítést.');
        } else {
            $message .= Yii::t('msg' ,'<br />Az új jelszóról nem sikerült elküldeni a felhasználónak az értesítő emailt, ezért kérem vegye fel vele a kapcsolatot!');
        }

        return array('success' => true, 'message' => $message);
    }

    public function deleteUser($id)
    {
        $errors = array();

        // magamat nem törölhetem
        if ($id == Yii::app()->user->id) {
            $errors[] = Yii::t('msg' ,'Saját magát nem törölheti.');
        } else {
            // töröljük a kapcsolatait
            UserProfile::model()->deleteAll('user_id = :user_id', array(':user_id' => $id));
            UserUserGroup::model()->deleteAll('user_id = :user_id', array(':user_id' => $id));
            UserPermission::model()->deleteAll('user_id = :user_id', array(':user_id' => $id));
        }

        if (!empty($errors)) {
            return array(
                'success'=>false,
                'message'=>Yii::t('msg' ,'Felhasználó törlése sikertelen!'),
                'errors'=>$errors
            );
        }

        $response_success_true = array(
            'success'=>true,
            'message'=>Yii::t('msg' ,'Felhasználó sikeresen törölve.')
            );
        $response_success_false = array(
            'success'=>false,
            'message'=>Yii::t('msg' ,'Felhasználó törlése sikertelen!'),
            'errors'=>array()
            );

        $rows_deleted = User::model()->deleteByPk($id);

        return $rows_deleted == 1 ? $response_success_true : $response_success_false;
    }
}