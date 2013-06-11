<?php

class EHttpRequest extends CHttpRequest
{
    /**
     * A böngészőben beállított preferált nyelvek listája, 2 karakterre csonkolva
     * A Yii nem ad metódust az összes nyelv elkérésére, csak az elsőre
     *
     * @return array
     */
    public function getPreferredLanguages()
    {
        if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
        {
            return array();
        }
        $browserLanguages = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
        for ( $i = 0; $i < count($browserLanguages); $i++ )
        {
            $language = explode( ';', $browserLanguages[$i] );
            $browserLanguages[$i] = substr($language[0],0,2);
        }

        return array_values(array_unique($browserLanguages));
    }

    /**
     * Ha már van session, akkor az abba beállított nyelv,
     * különben a usernek a böngészőben beállított preferált nyelvei közül az első olyan,
     * amelyen az alkalmzás is beszél,
     * ha nincs ilyen, akkor az alkalmazás nyelvei közül az első
     *
     * @return string 2 karakteres nyelv-kód
     */
    public function getPreferredAppLanguage()
    {
        if (isset(Yii::app()->user)) {
            return Yii::app()->language;
        }

        $user_languages = $this->getPreferredLanguages();
        $app_languages = array_keys(Yii::app()->params['languages']);

        foreach ($user_languages as $user_lang) {
            foreach ($app_languages as $app_lang) {
                if ($user_lang == $app_lang) return $user_lang;
            };
        }

        return $app_languages[0];
    }

    /**
     * A user által preferált nyelv, ha cookie-ban van tárolva, akkor az,
     * egyéként @see getPreferredAppLanguage()
     */
    public function getDefaultLanguage()
    {
        return isset($this->cookies['hsch_language']) ?
               $this->cookies['hsch_language']->value :
               $this->getPreferredAppLanguage();
    }

    public function setHeaderByMimeType($file_name, $mime_type)
    {
        header('Content-Type: '. $mime_type, true);
        header('Content-Disposition: attachment; filename="'.$file_name.'"', true);
        header('Content-Transfer-Encoding: binary', true);
        header('Pragma: no-cache', true);
        header('Cache-Control: public, must-revalidate', true);
    }

}