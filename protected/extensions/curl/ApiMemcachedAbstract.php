<?php


abstract class ApiMemcachedAbstract extends ApiAbstract
{
    /**
     * Cache tarolo.
     *
     * @var CacheAdapterRepcached
     */
    protected $cache;

    /**
     * Konstruktor.
     *
     * @param string $connectionName   A cache kapcsolat neve.
     * @param string $responseFormat   Valasz formatuma.
     * @param int    $timeOut          A keres max hossza masodpercekben.
     */
    protected function __construct(
        $connectionName = 'repcached.mobile_site',
        $responseFormat = self::RESPONSE_FORMAT_JSON, $timeOut = 6
    ) {
        parent::__construct($responseFormat, $timeOut);
        $this->setCache($connectionName);
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
        // TESZTELESI CELBOL
        if (
            $this->hasFallbackClass
            && (Config::get('modules.apifallback.enabled', false) || isset($_COOKIE['triggerApiFallback']))
        ) {
            throw new ApiException('Kikenyszeritett fallbakre iranyitas... (' . get_called_class() . ')', ApiException::EC_API_FORCE_FALLBACK);
        }

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
            $this->cache->put($cacheId, $result, $cacheTtl);
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
     * Kiepiti a Memcached kapcsolatot.
     *
     * @param string $connectionName   Kapcsolat neve
     * @throws Exception   A kapcsolat neve nem memcached-vel vagy repcached-vel kezdodik.
     */
    protected function setCache($connectionName)
    {
        // Ha memcached-et hasznal
        if (strpos($connectionName, 'memcached') === 0) {
            $this->cache = Cache::getMemcached($connectionName);
        }
        // Ha repcached-et hasznal
        elseif (strpos($connectionName, 'repcached') === 0) {
            $this->cache = Cache::getRepcached($connectionName);
        }
        else {
            throw new Exception(sprintf('Api szamara hasznalhatatlan kapcsolat: %s.', $connectionName));
        }
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
        // TESZTELESI CELBOL
        $triggerFallback = ($this->hasFallbackClass
            && (Config::get('modules.apifallback.enabled', false)
                || isset($_COOKIE['triggerApiFallback'])
            )
        ) ? true : false;

        if ($triggerFallback || empty($result) || $result['status'] != 'OK') {
            $message = sprintf('Hiba az API (' . get_called_class() . ') hivas soran (hibakod: %s).', $result['error_code']);
            if (!$this->hasFallbackClass) {
                trigger_error($message, E_USER_ERROR);
            }
            throw new ApiException($message, $result['error_code']);
        }

        return $result['data'];
    }

}
