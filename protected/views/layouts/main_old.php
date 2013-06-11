<?php $http_host = $_SERVER['HTTP_HOST']; ?>
<?php $system_base_url = Yii::app()->params['no_script_name'] ? '': $_SERVER['SCRIPT_NAME']; ?>
<?php $web_root_url = rtrim(dirname($system_base_url), '/'); ?>
<?php $image_base_url = rtrim($web_root_url, '/') . '/images'; ?>
<?php $app_language = 'hu'; ?>
<?php $cs = Yii::app()->getClientScript(); ?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="hu" />

<?php $cs->registerScript('http_host','var http_host = "'. $http_host. '";', CClientScript::POS_HEAD ) ?>
<?php $cs->registerScript('systemBaseUrl','var systemBaseUrl = "'. $system_base_url. '";', CClientScript::POS_HEAD ) ?>
<?php $cs->registerScript('imageBaseUrl','var imageBaseUrl = "'. $image_base_url . '";', CClientScript::POS_HEAD ) ?>

	<script type="text/javascript" src="<?php echo Yii::app()->getBaseUrl() . extjs4Config::get('extjs4_js_dir') . "ext-all" . (Yii::app()->params['extjs_debug'] ? "-debug" : "") . ".js" ?>"></script>

<?php
// TODO plugin-okat configolhatóan kellene
$ext = new Extjs4Plugin(array('theme'=>'blue'), Yii::app()->params['extjs_plugins']);
$ext->load();

$appName = Yii::app()->params["extjs_appname"];
	
?>

	<script type="text/javascript">


	Ext.Loader.setConfig({enabled: true});
	Ext.Loader.setPath('Ext.ux.app', './js/extjs4/ux/app');
	Ext.require([
			'Ext.layout.container.*',
			'Ext.resizer.Splitter',
			'Ext.window.Window',
			'Ext.form.*',
            'Ext.window.MessageBox',
            'Ext.tip.*'
	]);

	var MESSAGES = {
		CONFIRM_EXIT_TITLE: 'Kilépés megerősítése',
		CONFIRM_EXIT_MSG: 'Nem mentett adatok vannak, biztosan kilép?',
		BUTTON_CLOSE: 'Bezár',
		LOADING: 'Betöltés...',
		ERROR: 'Hiba',
		ERROR_DATA: 'Hibás adatkérés.',
		ERROR_FORM: 'Az űrlap kitöltése hibás, kérem ellenőrizze!',
		ERROR_CONNECTION: 'Hiba a kapcsolatban, kérjük próbálja meg ismét.',
		LOGIN_TIMEOUT_TITLE: 'Kérjük lépjen be ismét',
		LOGIN_TIMEOUT_MSG: 'A biztonsági időkorlát lejárt, kérjük nyomja meg az OK gombot, majd adja meg adatait a bejelentkezéshez.',
		INVALID_REQUEST: 'Jogosulatlan adat kérés',
		ERROR_REQUEST: 'Sikertelen adat kérés',
		ERROR_FORM: 'Hiba az űrlapon',
		SAVE_WAIT_TITLE: 'Mentés',
		SAVE_WAIT_MESSAGE: 'Adatok rögzítése...'
	};

	var theApp = null;
	Ext.onReady(function() {
		theApp = Ext.create('Ext.ux.app.Application', '<?php echo $appName ?>');
		theApp.initApp({
			userAuthenticated: <?php echo Yii::app()->user->isAuthenticated() ? 'true' : 'false' ?>,
			defaultLanguage: '<?php echo $app_language ?>',
			loginUrl: '<?php echo Yii::app()->user->loginUrl ?>',
			systemBaseUrl: '<?php echo Yii::app()->params['no_script_name'] ? '': $_SERVER['SCRIPT_NAME']; ?>',
			background: {
				image: '<?php echo  $web_root_url . '/images/' . Yii::app()->params['background']['image'] ?>', 
				color: '<?php echo Yii::app()->params['background']['color'] ?>',
				tile: <?php echo Yii::app()->params['background']['tile'] ? 'true' : 'false' ?>,
				fit: false
			}
		});

		<?php
		if (Yii::app()->user->isAuthenticated()) {
			$last_url = Yii::app()->user->getAttribute(Yii::app()->params['last_visited_url_param_name']);
			$last_params = Yii::app()->user->getAttribute(Yii::app()->params['last_visited_url_jx_params_name']);
			if ($last_url ==!"") {
				?>
					theApp.loadModule('<?php echo $last_url ?>', Ext.decode(unescape('<?php echo $last_params ?>')));
				<?php
			}
		}
		?>
	});



	</script>

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<script type="text/javascript" src="<?php echo Yii::app()->getBaseUrl() . extjs4Config::get('extjs4_js_dir') . "locale/ext-lang-$app_language.js" ?>"></script>
</head>

<body>
	<span id="app-msg" style="display:none;"></span>
</body>
</html>