<?php


abstract class ApiAbstract extends Singleton
{

    /** Post metodus. */
    const METHOD_POST = 'post';
    /** Get metodus. */
    const METHOD_GET  = 'get';

    /** Json dekoder. */
    const RESPONSE_FORMAT_JSON = 'json';

    /**
     * Kodek.
     *
     * @var string
     */
    protected $codec;

    /**
     * A keres max hossza masodpercekben.
     *
     * @var int
     */
    protected $timeOut;

    /**
     * Hanyszor probalja ujra a kerest, sikertelenseg eseten.
     *
     * @var int
     */
    protected $retryCount = 1;

    /**
     * Feldolgozza visszakapott eredmenyt.
     *
     * @param mixed $result   A visszakapott eredmeny.
     *
     * @throws Exception   Hibas eredmeny eseten.
     *
     * @return mixed   Az eredmeny adat resze.
     */
    abstract protected function processResult($result);

    /**
     * Konstruktor.
     *
     * @param string $responseFormat   Valasz formatuma.
     * @param int    $timeOut          A keres max hossza masodpercekben.
     */
    protected function __construct($responseFormat = self::RESPONSE_FORMAT_JSON, $timeOut = 100)
    {
        $this->setCodec($responseFormat);
        $this->timeOut = $timeOut;
    }

    /**
     * Beallitja a keres max hosszat masodpercekben.
     *
     * @param int $timeOut   A keres max hossza masodpercekben.
     *
     * @return void
     */
    public function setTimeOut($timeOut)
    {
        $this->timeOut = $timeOut;
    }

    /**
     * Vegrehajtja az API hivast.
     *
     * @param string $url          A keres url-je.
     * @param string $method       Az adatkuldes metodusa. (@uses self::METHOD_*)
     * @param array  $parameters   A hivas GET vagy POST parameterei az adatkuldes metodusatol fuggoen.
     * @param array  $cookies      A hivas COOKIE parameterei.
     * @param array  $files        File atvitelhez tartozo adatok.
     *
     * @return mixed   A hivas eredmenye.
     */
    protected function call($url, $method = self::METHOD_GET, $parameters = array(), $cookies = array(),
                            $files = array(), $headers = array())
    {
        // TODO: Ez elegge ideiglenes, valamit ki kell talalni hogy megbizhatobb legyen [emul]
        for ($tryCount = 1; $tryCount <= $this->retryCount + 1; $tryCount++) {
            try {
                $response = $this->execute($url, $method, $parameters, $cookies, $files, $headers);
                break;
            }
            catch (Exception $e) {
                if ($tryCount < $this->retryCount + 1) {
                    trigger_error($e->getMessage(), E_USER_ERROR);
                }
                else {
                    throw $e;
                }
            }
        }

        $result   = $this->codec->decode($response);
        return $this->processResult($result);
    }

    /**
     * Vegrehajtja a tenyleges kerest.
     *
     * @param string $url          Az url.
     * @param string $method       Az adatkuldes metodusa. (@uses self::METHOD_*)
     * @param array  $parameters   A hivas GET vagy POST parameterei az adatkuldes metodusatol fuggoen.
     * @param array  $cookies      A hivas COOKIE parameterei.
     * @param array  $files        File atvitelhez tartozo adatok.
     *
     * @return mixed   A keresre kapott valasz.
     *
     * @throws Exception   Ha sikertelen volt a kuldes.
     */
    protected function execute($url, $method, $parameters, $cookies, $files, $headers)
    {
        try {
            $curl = new Curl($url, $method, true, $this->timeOut);
            switch ($method) {
                case self::METHOD_GET:
                    $curl->setGet($parameters);
                    break;

                case self::METHOD_POST:
                    if (empty($files)) {
                        $curl->setPost($parameters);
                    }
                    else {
                        $curl->setFile($parameters, $files);
                    }
                    break;
            }
            $curl->setCookie($cookies);

            if (!empty($headers)){
                $curl->setCustomHeader($headers);
            }
//print_r($curl); exit;
            return $curl->exec();
        }
        catch (CurlException $e) {
            throw new Exception(sprintf('Hiba a cURL kuldes soran: %s.', var_export($curl->getInfo(), true)));
        }
    }

    /**
     * Beallitja a kodeket.
     *
     * @param string $responseFormat   A valasz formatuma.
     *
     * @return void
     */
    protected function setCodec($responseFormat)
    {
        switch ($responseFormat) {
            case self::RESPONSE_FORMAT_JSON:
                $this->codec = new CodecAdapterJson();
                break;

            default:
                throw new Exception(sprintf('Ervenytelen API valasz formatum: %s.', $responseFormat));
        }
    }
}