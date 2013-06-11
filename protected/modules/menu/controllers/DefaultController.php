<?php

class DefaultController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionGetUserMenu()
	{
	    $user_id = Yii::app()->user->getId();

	    $menu = $this->getMenu($user_id);

	    $this->render('/default/menu.js', array('menuitems' => $menu));
	}

    private function getMenu($user_id) {
        $system_base_url = Yii::app()->params['no_script_name'] ? '': $_SERVER['SCRIPT_NAME'];
        $web_root_url = rtrim(dirname($system_base_url), '/');

        // paraméterek csak a function cache miatt kellenek!
        $mainMenu = $this->constructMenu( Yii::app()->params['menu'] );
        $mainMenu[] = array('xtype' => 'tbfill');
        /*$mainMenu[] = array(
            'xtype' => 'image',
            'src'   => $web_root_url . '/images/enefex_mini_logo.gif',
            'height'=> 23,
            'width' => 92,
            'listeners' =>
                array(
                    'click' => array(
                        'element' => 'el',
                        'fn' => 'function(){ window.open("http://www.enefex.hu", "Enefex"); }'
                    )
                )

        );*/
        return $mainMenu;
    }


    /**
     * Összeállítja a felhasználó menüjét, figyelembe véve jogosultságait
     *
     * Letiltja azokat a menüpontokat, amelyekhez nincs jogosultsága
     *
     * A menü szerkezetét az /apps/fm/config/menu.yml-ben kell deklarálni
     *
     * @param array $menu_array Menü deklaráció
     * @return JS
     */
    private function constructMenu($menu_array) {
        $item_count = count($menu_array);
        $menu = array();
        //$env  = sfConfig::get('sf_environment');
        // foreach MAIN MENU ITEMS in $menu_config
        foreach ($menu_array as $key => $item) {

            $menu_item = array();

            if (is_array($item)) {

                $is_separator = isset($item['text']) ? $item['text'] == '-' : false;
                $is_module = isset($item['module']) && $item['module'] ? true : false;
                $is_active = $is_module || (isset($item['url']) && $item['url']);
                $has_submenu = isset($item['menu']) ? is_array($item['menu']) : false;

                //$include_env = isset($item['only_env']) ? explode(',', $item['only_env']) : array($env);
                //$exclude_env = isset($item['exclude_env']) ? explode(',', $item['exclude_env']) : array();
                //$diff_env = array_diff($include_env, $exclude_env);
                //if (!in_array($env, $diff_env)) continue;

                if ($is_separator) {
                    $menu_item = "'-'";
                } else {

                    if ($is_module) {
                        $url_items = $item['url'];
                        if (strpos($url_items, '?')>0) list($url_items, $dummy) = explode('?', $url_items, 2);
                        $url_items = explode('/', $url_items);

                        $action_name = array_pop($url_items);
                        $controller_name = array_pop($url_items);
                        $module_name = count($url_items) ? array_pop($url_items) : 'site';

                        // ha olyan module-t kérünk, ami egyértelműen egy komponens engedélyezésétől függ és az nincs engedélyezve, akkor kimarad ez a menüpont
                        //if (!$this->app_config->isModuleEnabled($module_name)) continue;

                        if (!isset($item['params'])) $item['params'] = array();
												// a parameterekhez hozzavesszuk az URL parametereket is
												$item['params'] = array_merge($this->getUrlParams($item['url']), $item['params']);
												
                        if (!isset($item['text'])) $item['text'] = '?????';
                        if (!isset($item['tooltip'])) $item['tooltip'] = '';
                        if (!isset($item['cls'])) $item['cls'] = '';
                        if (!isset($item['iconCls'])) $item['iconCls'] = '';
                        if (!isset($item['show_above'])) $item['show_above'] = false;
                        if (!isset($item['slow'])) $item['slow'] = false;
                        if (!isset($item['disabled'])) $item['disabled'] = false;

                        // le van tiltva a menü, ha a config alapján ki van kapcsolva, vagy a user-nek nincs hozzá joga
                        $item['disabled'] = $item['disabled'] || !$this->getUserHasCredential($module_name, $controller_name, $action_name);

                    } else {
                        if (!isset($item['url'])) $item['url'] = '';
                        if (!isset($item['params'])) $item['params'] = array();
                        if (!isset($item['tooltip'])) $item['tooltip'] = '';
                        if (!isset($item['cls'])) $item['cls'] = '';
                        if (!isset($item['iconCls'])) $item['iconCls'] = '';
                        if (!isset($item['disabled'])) $item['disabled'] = false;
                        $item['show_above'] = false;
                        $item['slow'] = false;
                    }

                    $menu_item['text'] = $item['text'];

                    if ($item['cls']) $menu_item['cls'] = $item['cls'];
                    if ($item['iconCls']) $menu_item['iconCls'] = $item['iconCls'];
                    if ($item['tooltip']) $menu_item['tooltip'] = "{ text:'".$item['tooltip']."' }";
                    if ($item['disabled']) $menu_item['disabled'] = true;
                    if ($item['slow']) $menu_item['slow'] = true;

                    // is a single window, or fills the content panel
                    if (isset($item['show_above']) && $item['show_above']) $menu_item['show_above'] = 'true';

                    if ($is_module) {
                        $menu_item['handler'] = "this.onItemChoosen";
                        $menu_item['scope'] = new ExtCodeFragment("this");
                    	$menu_item['url'] = '/'.$item['url'];

                        $menu_item['openDialog'] = (isset($item['openDialog']) && $item['openDialog'])?true:false;
                    	if ($item['params']) $menu_item['params'] = $item['params'];
                    } else if (substr($item['url'],0,7) == 'http://') {
                            $menu_item['handler'] = "this.onExternalItemChoosen";
		                        $menu_item['scope'] = new ExtCodeFragment("this");
                            $menu_item['url'] = $item['url'];
                            $menu_item['window_open'] = isset($item['window_open']) ? $item['window_open'] : 0;
                    } else if (isset($item['is_static']) && $item['is_static'] == 1) {
                            $menu_item['handler'] = "this.onStaticItemChoosen";
                            $menu_item['scope'] = new ExtCodeFragment("this");
				                    $menu_item['url'] = $item['url'];
                            $menu_item['window_open'] = isset($item['window_open']) ? $item['window_open'] : 0;
                    }

                    if ($has_submenu) {
                        $menu_item['menu'] = array( 'items' => $this->constructMenu($item['menu']) );
                    }

                } // END if not separator

                $menu[] = $menu_item;

            } // END if is_array()
        } // END foreach

        return $menu;

    }
		
		/**
		 * 
		 * @param string    URL
		 * @return string[] 
		 */
		protected function getUrlParams($url)
		{
			$params = array();
			
			$urlParts = explode('?', $url);
			if (count($urlParts)==2) {
				foreach (explode('&', $urlParts[1]) as $p) {
					$pParts = explode("=", $p);
					if (count($pParts)==2) {
						$params[$pParts[0]] = $pParts[1];
					}
				}
			}
			return $params;
		}

    public function actionGetUserStatusBar()
    {
        $db_user = User::model()->findByPk(Yii::app()->user->getId()); // kell ez? nem lehet közvetlenül?

		//$this->user_groups = ''; //$db_user->getGroupNamesImploded(); // TODO implement
        $user_groups_datas = UserManager::getInstance()->getAssociatedGroups(Yii::app()->user->getId());
        $user_groups = array();

        foreach ($user_groups_datas["data"] as $ug) {
            $user_groups[] = $ug["name"];
        }

        $user_groups_string = count($user_groups) ? implode(', ', $user_groups) : Yii::t('msg','...csoport nélkül');

		$this->render('/default/statusbar.js', array('user_updated_at' => $db_user->last_login, 'user_name' => $db_user->username, 'user_groups' => $user_groups_string));
    }
}