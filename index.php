<?php
// change the following paths if necessary
$yii=dirname(__FILE__).'/../yii_1.1.10/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';


// remove the following lines when in production mode
//defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
//defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);

setlocale(LC_ALL, array('en_US.utf8', 'en_US', 'american'));

//$app = Yii::createWebApplication($config);
include_once dirname(__FILE__).'/protected/components/HotelWebApplication.php';

$app = Yii::createApplication('HotelWebApplication', $config);
$app->setTimeZone($app->params['timezone']);

$app->run();
