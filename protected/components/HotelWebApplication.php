<?php

Class HotelWebApplication extends CWebApplication
{
    public function displayException($exception)
    {
        $http_host = $_SERVER['HTTP_HOST'];
        $system_base_url = Yii::app()->params['no_script_name'] ? '': $_SERVER['SCRIPT_NAME'];
        $web_root_url = rtrim(dirname($system_base_url), '/');

        echo '<html>';
        echo '<head>';
        echo '<link href="'.$web_root_url.'/css/exception.css" type="text/css" rel="stylesheet">';
        echo '</head>';
        echo '<h1>'.Yii::t('msg',"Hiba történt az alkalmazás futtatása során").'</h1>';

        if(YII_DEBUG)
        {
            echo '<h2>'.get_class($exception).': '.$exception->getMessage()."</h2>\n";
            echo '<p>'.$exception->getFile().':'.$exception->getLine().'</p>';
            echo '<pre>'.$exception->getTraceAsString().'</pre>';
        }
        else
        {
            echo '<p>'.Yii::t('msg',"A hibakereséshez szükséges információk:").'</p>\n';
            echo '<p><strong>'.$exception->getMessage().'</strong><br />';
            echo $exception->getFile().':'.$exception->getLine().'</p>';
            echo '<h2>'.Yii::t('msg',"Kérjük vegye fel a kapcsolatot a fejlesztővel!").'</h2>\n';
        }

        echo '</html>';
    }


}