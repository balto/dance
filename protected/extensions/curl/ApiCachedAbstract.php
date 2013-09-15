<?php


abstract class ApiCachedAbstract extends ApiAbstract
{
    /**
     * Cache tarolo.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Konstruktor.
     *
     * @param string $responseFormat   Valasz formatuma.
     * @param int    $timeOut          A keres max hossza masodpercekben.
     */
    protected function __construct(
        $responseFormat = self::RESPONSE_FORMAT_JSON, $timeOut = 6
    ) {
        parent::__construct($responseFormat, $timeOut);
        $this->setCache();
    }

    /**
     * Vegrehajtja az API hivast.
     *
     * @param string $url              A keres url-je.
     * @param string $method           Az adatkuldes metodusa. (@uses self::METHOD_*)
     * @param array  $parameters       A hivas GET vagy POST parameterei az adatkuldes metodusatol fuggoen.
     * @param array  $cookies          A hivas COOKIE parameterei.
     * @param array  $files            File atvitelhez tartozo adatok.
     * @param bool   $forceExecution   Szerver hivas vegrehajtasanak force-olasa (nem probal cache-bol olvasni).
     * @param int    $cacheTtl         Cache idolimit masodpercben
     *
     * @return mixed   A hivas eredmenye.
     */
    protected function call(
        $url, $method = self::METHOD_GET, $parameters = array(), $cookies = array(),
        $files = array(), $forceExecution = false, $cacheTtl = 0
    ) {

        $cacheId = $this->generateCacheId($url, $parameters);
        if (!$forceExecution && $cacheTtl > 0) {
            // Ha nincs force-olva a vegrehajtas es kell cachelni.
            if (($result = $this->cache->get($cacheId)) !== false) {
                // Ha a cache-ben van adat.
                return $result;
            }
        }

        $result = parent::call($url, $method, $parameters, $cookies, $files);

        // Ha nem kell cachelni, nem nyulunk memcachehez.
        if ($cacheTtl > 0) {
            $this->cache->set($cacheId, $result, $cacheTtl);
        }

        return $result;
    }

    /**
     * General egy cache azonositot.
     *
     * @param string $url          Az url.
     * @param array  $parameters   A keres parameterei.
     *
     * @return string   A generalt cache azonosito.
     */
    protected function generateCacheId($url, $parameters)
    {
        $cacheId = $url . json_encode($parameters);

        return md5($cacheId);
    }

    /**
     * Kiepiti a Cache kapcsolatot.
     *
     *
     * @throws Exception   A kapcsolat neve
     */
    protected function setCache()
    {
       $this->cache = Yii::app()->cache;
    }

    /**
     * Feldolgozza visszakapott eredmenyt.
     *
     * @param mixed $result   A visszakapott eredmeny.
     *
     * @throws Exception   Hibas eredmeny eseten.
     *
     * @return mixed   Az eredmeny adat resze.
     */
    protected function processResult($result)
    {
        return $result['data'];
    }

}
