<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'HSCH',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import' => array(
		'application.models.*',
		'application.components.*',
		'application.components.validators.*',
		'application.extensions.phputils.*',
		'application.extensions.behaviors.*',
		'application.extensions.sfCsvPlugin.lib.*',
		'application.extensions.extjs4Plugin.*',
		'application.extensions.extjs4Plugin.widgets.*',
		'application.extensions.sfDate.*',
		'application.extensions.sqlutils.*',
        'application.extensions.tcpdf.*',
	),

    'sourceLanguage'=>'en',
    'language' => 'hu',

	'modules'=>array(
		// uncomment the following to enable the Gii tool

		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'passwd',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
		 	// http://php53.in.publishing.hu/bene/workspace/energetikus/gii/default/login
			//'ipFilters'=>array('192.168.4.122','::1'), // bene:192.168.4.122, oszlanyi:192.168.4.135, varga: 192.168.4.1
		),
		'mainModule' => array(
				'secure' => true,
		),

		'menu'=>array(
		    'secure'=>true,
		),
		'codetable'=>array(
		    'secure'=>true,
        ),
		'user'=>array(
		    'secure'=>true,
		),
        'member'=>array(
            'secure'=>true,
        ),     
        'campaign' => array(
        	'secure' => true,
        ),
        'location' => array(
        	'secure' => true,
        ),
        'interface' => array(
        	'secure' => true,
        ),
        'ticket' => array(
        	'secure' => true,
        ),
        'price' => array(
        	'secure' => true,
        ),
        
    ),

	// application components
	'components'=>array(
		'request'=>array(
            'enableCookieValidation'=>true,
			//'enableCsrfValidation'=>true,
            'class' => 'EHttpRequest',
        ),
		'user'=>array(
		    'class'=>'EWebUser',
			// enable cookie-based authentication
			//'allowAutoLogin'=>true,
            'loginUrl'=>'site/login',
		),
		'urlManager'=>array(
			'urlFormat'=>'path',
		    'showScriptName'=>false,
			'rules'=>array(
		        'index' => 'site/index',
				'login' => 'site/login',
		        'logout' => 'site/logout',
		        'user/profile' => 'user/defaultUser/profile',
				'file/<file_descriptor_id:\w+>/<timestamp:\w+>/<user_id:\w+>/<user_ip:\w+>/<hash:\w+>' => 'file/default/downloadFile',
				'<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/<controller>/<action>',
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
			),
		),

		/*
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),
        */

		/*'db'=>array(
			'connectionString' => 'mysql:host=127.0.0.1;dbname=hotel2',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'vargabal',
			'charset' => 'utf8',
		    //'schemaCachingDuration' => 3600,
		    'class' => 'EDbConnection',
		    'enableParamLogging' => true,
		),*/
		'db'=>array(
				'connectionString' => 'mysql:host=localhost;dbname=vargabal',
				'emulatePrepare' => true,
				'username' => 'vargabal',
				'password' => '20SoniR77012',
				'charset' => 'utf8',
				//'schemaCachingDuration' => 3600,
				'class' => 'EDbConnection',
				'enableParamLogging' => true,
		),

		'session' => array(
            'class' => 'application.components.EDbHttpSession',
            'connectionID'=>'db',
            'cookieMode'=>'only',
            'sessionName'=>'HSCH',
            'sessionTableName' => 'sessions',
			'autoCreateSessionTable'=>false,
            'timeout' => 60*60*24,
            'cookieParams' => array(
                'lifetime' => 60*60*24*365,
                'path' => '/',
                'secure' => false,
                'httponly' => true,
            ),
        ),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
				    'logFile'=>'error.log',
					'levels'=>'error',
                    'maxFileSize'=>10000,
                    'maxLogFiles'=>5,
				),
                array(
                    'class'=>'CFileLogRoute',
                    'logFile'=>'warning.log',
                    'levels'=>'warning',
                    'categories'=>'sql.* php.* mail.*',
                    'maxFileSize'=>10000,
                    'maxLogFiles'=>5,
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'logFile'=>'business.log',
                    'levels'=>'warning',
                    'categories'=>'business.*',
                    'maxFileSize'=>10000,
                    'maxLogFiles'=>5,
                ),
                array(
                    'class' => 'CWebLogRoute',
                    'categories' => 'sql.*',
                    'levels' => '',
                    'showInFireBug' => true,
                ),
                // uncomment the following to show log messages on web pages
/*
				array(
					'class'=>'CWebLogRoute',
				    'showInFireBug'=>true
				),
*/
			),
		),
		'cache'=>array(
		    //'class'=>'system.caching.CDummyCache', //'system.caching.CFileCache',
			'class'=>'system.caching.CFileCache', //'system.caching.CFileCache',
		),
		'swiftMailer' => array(
		    'class'=>'ext.swiftMailer.SwiftMailer',
		),

	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
       'background'=>array(
           'image'=>'kch_logo.gif',  // az image könyvtár alatt kell lennie
           'color'=>'#000000',
           'tile' =>false,  // false: 1x középre, mellette körben color szín; true: csempe (a color nem jelenik meg)
           'fit'  =>false,  // false: a kép eredeti méretben; true: a kép kitölti a "desktop"-ot, torzul (ilyenkor a tile paraméter nem érdekes)
       ),
	   'menu'=>require(dirname(__FILE__).'/menu.php'),
	   'locale'=>'hu_HU.utf8',
	   'timezone'=>'Europe/Budapest',
	   'db_timezone' => 'GMT',
	   'db_date_format'=>'Y-m-d',
	   'db_time_format'=>'H:i:s',
	   'db_datetime_format'=>'Y-m-d H:i:s',
	   'yii_date_format'=>'yyyy-MM-dd',
	   'no_script_name'=>false,
	   'extjs_app_namespace'=>'HSCH',
	   'extjs_pager_max_per_page'=>50,
	   'extjs_combo_pager_max_per_page'=>11,
	   'extjs_appname' => 'HSCH',
       'languages'=>array(
            'hu'=>array('source'=>'Magyar', 'target' => 'Magyar'),
       ),
	   'extjs_plugins' => array(
	        'js' => array(
                    'examples.js',
                    'ux/Ext.ux.grid.PageSize.js',
                    'ux/Ext.ux.grid.RowActions.js',
                    'ux/form/MultiSelect.js',
                    'ux/form/ItemSelector.js',
                    'ux/Ext.ux.grid.plugin.HeaderFilters.js',
                    'ux/form/TimePickerField.js',
                    'ux/form/DateTimePicker.js',
					'ux/form/DateTimeField.js',
					'ux/Ext.ux.ColorField.js',
                    'ux/Ext.ux.form.AddField.js',
                    'ux/Ext.ux.RowExpander.js',
                    'ux/Ext.ux.NumericField.js',
					'ux/Ext.override.js',
	        ),
	        'css' => array(
	            'ux/Ext.ux.grid.RowActions.css',
	            'ux/ItemSelector.css',
                'example.css',
                '../common.css',
	        	'../icons.css',
	            '../form.css',
                '../main.css',
	        )
	   ),
	   'extjs_date_format'=>'Y-m-d',
	   'extjs_time_format'=>'H:i',
	   'extjs_datetime_format'=> 'Y-m-d H:i',
       'extjs_datetime_sec_format'=> 'Y-m-d H:i:s',
	   'extjs_debug'=>false,
	   'extjs_number_format'=>'0.0,00/i',
	   'extjs_number_format_low_precision'=>'0.0/i',
       'extjs_money_format'=>'0.0,00 Ft/i',
	   'extjs_money_format_low_precision'=>'0.0 Ft/i',
	   'excel_number_format' => '0.00',
	   'excel_number_format_low_precision' => '0',
	   'php_number_format'=>array(//nyomtatáshoz kell
	       'decimals'=>2,
	       'dec_point'=>',',
	       'thousands_sep'=>'.',
	   ),
       'php_number_format_low_precision'=>array(//nyomtatáshoz kell
	       'decimals'=>0,
	       'dec_point'=>',',
	       'thousands_sep'=>'.',
        ),
        'php_locale_date_format'=>array(
            'short'=>'%Y-%m-%d',
            'long'=>'%Y. %B %e.',
        ),
	   'password_validator_pattern'=>'.*(?=.{6,20})(?=.*\d)(?=.*[a-záéíóöőúüű])(?=.*[A-ZÁÉÍÓÖŐÚÜŰ]).*',
	   'password_validator_msg'=> 'A jelszó hossza 6 - 20 karakter között legyen,<br />tartalmazzon legalább 1 kis- és nagybetűt, valamint számot is!',
	   'last_visited_url_param_name'=>'lasturl',
	   'last_visited_url_jx_params_name'=>'lasturl_jx_params',
	   'server-status'=>'php53/server-status',
	   'long_php_process_timeout'=>500,
	   'bulk_insert_max_record_count'=>500, // 500, egyszerre legfeljebb ennyi sort próbálunk insert-tel beszúrni
		'cache_keys'=>array(
                'query' => 'queries',
                'between_month_day' => 'between_month_day',
	   ),
	   /*'email'=>array(
	       'host'=>'127.0.0.1',
	   	   'port'=>25,
           'senderEmail'=>'info@publishing.hu',
	       'senderName'=>'Publishing Factory Kft.',
	   	   'message-footer' => "\n---\nEz egy automatikusan generált levél, kérem ne válaszoljon rá!\n",
	   ),*/
	  /* 'developer_alert'=>array(
	   	   'recipientEmail'=>'developers@publishing.hu',
	       'recipientName'=>'Hotel fejlesztők',
	       'subject'=>'Hiba a Hotel2 rendszerben',
	   ),
       'customer_service_email'=>array(
           'recipientEmail'=>'bene@publishing.hu',
           'recipientName'=>'Ügyfélszolgálat',
           'message-footer' => "<br /><br />\n---<br />\nEz egy automatikusan generált levél, kérem ne válaszoljon rá!<br />\n",
       ),*/
	   /*'failed_logins'=>array(
            'notify'=>array(
                'count'=> 5,
                'user'=> true,    # a felhasználót magát értesíteni kell-e a sikertelen loginról
                'admin'=> array('support@publishing.hu' => 'Hotel2 Support Team'),   # értesítendő admin(ok)
                'email'=>array(
                    'subject-template'=> "Többszöri sikertelen bejelentkezés - %s",
                    'message-template'=> "%s (%s), vagy valaki az ő azonosítójával 5 alkalommal sikertelenül próbált a %s Hotel2 rendszerbe bejelentkezni.\n",
                ),
            ),
            'inactivate'=>array(
                'count'=> 10,
                'user'=> true,    # a felhasználót magát értesíteni kell-e a kizárásról
                'admin'=> array('support@publishing.hu' => 'Hotel2 Support Team'),   # értesítendő admin(ok)
                'email'=>array(
                    'subject-template'=> "Felhasználó letiltása többszöri sikertelen bejelentkezés miatt - %s",
                    'message-template'=> "%s (%s), vagy valaki az ő azonosítójával 10 alkalommal sikertelenül próbált a %s Hotel2 rendszerbe bejelentkezni, ezért a rendszer letiltotta a felhasználót. \nAz újbóli aktiválásért forduljon rendszer adminisztrátorhoz.",
                ),
            ),
            'generate_password'=>array(
                'email'=>array(
                    'subject-template'=> "Új bejelentkezési jelszó az HSCH rendszerben",
                    'message-template'=> "%s (%s) felhasználó új jelszava a %s HSCH rendszerben:\n%s\n\n Ezt a jelszót az első sikeres belépés után meg kell változtatnia.",
                ),
            ),
        ),*/
	   'exclusiveTasks'=>array(
/*	       'background' => array( // az alábbi taskok közül csak egy lehet aktív
	           'background\/aggregate',
	           'background\/calcVirtuals',
	           'catch',
	       ),
*/
	   ),
       'CSV' => array(
           'locale' => 'en_US.iso-8859-1',
           'charset' => 'ISO-8859-1',
           'delimiter' => ",",    // mező elválasztó jel
           'enclosure' => '"',    // ha az elválasztó jel szerepel a mező értékében, akkor ilyen jelek közé kell zárni a mező teljes tartalmát
           // tizedes vessző v. pont - nem kell konfigurálni, e kettő bármelyikét rendben feldolgozzuk, mást meg senki sem használ
           // ezres elválasztó - nem kezeljük, az Excel sem exportálja ki egyszerű "Szám" formátumot beállítva
           'dateTimeFormat' => 'Y-n-j H:i',    // http://hu.php.net/manual/en/datetime.createfromformat.php szerint paraméterezhető
           // ha a 'dateTimeFormat' => '', akkor a http://hu.php.net/manual/en/datetime.formats.php alatt leírt formázások használhatók
           'comment' => '//',
       ),
	   'salt_for_hash' => 'nouhUGUIGBo879BIHbkl',
	   'app_file_download_grace_time' => 300,
       'download_use_subdomain' => false,
       'visibility_tables' => array(),
       'cache_config' => array( //egyelőre a visibility használja
            'class' => 'CFileCache', // CFileCache
            'servers' => array(
                array('host'=> '192.168.4.214', 'port' => '11211', 'weight' => 60),
                array('host'=> '192.168.4.215', 'port' => '11211', 'weight' => 40),
            )
       ),
       'limit_to_free_cond_holidays' => 30, // napokban értendő: 30 napon belüli feltételes foglalásokat fel kell szabadítani
       'default_interest_calc_grace_time' => 0, //napokban értendő: a számla lejárati dátuma után még 30 napig nem számolunk késedelmi kamatot
       'right_to_comission_user_group_id' => 9, //jutalekra jogosult user tipus id
       'teacher_user_group_id' => 6, //tanár user tipus id
	),
);