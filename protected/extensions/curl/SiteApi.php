<?php

class SiteApi extends ApiAbstract
{
    /**
     * Singleton peldany.
     *
     * @var Api
     */
    protected static $instance;

    /**
     * Visszaadja az objektum peldanyat.
     *
     * @return
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * Konstruktor.
     */
    protected function __construct()
    {
        parent::__construct(self::RESPONSE_FORMAT_JSON);
    }

    protected function callApi($service, $datas = array(), $format = self::RESPONSE_FORMAT_JSON, $headers = array())
    {
        if (empty($service) && !is_string($service)) {
            throw new ApiException('Ervenytelen service-t akartunk meghivni az Oranum API iranyaba.');
        }

        $serviceUrl = Yii::app()->params['apiUrl'] . $service;

        if (empty($serviceUrl)) {
            throw new ApiException('Ervenytelen service-t akartunk meghivni az API iranyaba: '.$serviceUrl);
        }

        $result = $this->call($serviceUrl, self::METHOD_POST,
            $datas,
            array(),
            array(),
            $headers
        );

        return (!$this->hasError($result) ? $result : false);
    }

    protected function hasError($result)
    {
        return (is_array($result) && isset($result['errors']) && count($result['errors']) != 0);
    }

    public static function getClientRealIpAddress()
    {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $tmp = explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]);
            if (!empty($tmp)) {
                return $tmp[0];
            }
        } else if (isset($_SERVER["HTTP_X_FORWARDED"])) {
            return $_SERVER["HTTP_X_FORWARDED"];
        } elseif (isset($_SERVER["HTTP_X_CLUSTER_CLIENT_IP"])) {
            return $_SERVER["HTTP_X_CLUSTER_CLIENT_IP"];
        } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_FORWARDED"])) {
            return $_SERVER["HTTP_FORWARDED"];
        } elseif (isset($_SERVER["REMOTE_ADDR"])) {
            return $_SERVER["REMOTE_ADDR"];
        } else {
            return '';
        }
    }



    /**
     * Hozzaad az oranum queue-hoz egy elemet.
     *
     * @param string $type   A queue elem tipusa.
     * @param string $data   A feldolgozo adatatokat tartalmaza, json encode-olt tomb.
     *
     * @return void
     */
    public function addElementToQueue($type, $data)
    {
        $res = $this->callApi(
            self::SERVICE_QUEUE_ADD,
            array(
                'type' => $type,
                'data' => $data,
                'src'  => 'community'
            )
        );
    }

    protected function processResult($result)
    {
        if (!is_string($result) && !is_numeric($result) && !is_array($result) && empty($result)) {
            trigger_error('Az API hivasra nem erkezett valasz.', E_USER_NOTICE);

            throw new Exception('Az API hivasra nem erkezett valasz.');
        }

        return $result;
    }
}
