<?php

class Curl
{
    /**
     * Curl objektum.
     *
     * @var resource
     */
    private $handle;

    /**
     * A cimzett url-je.
     *
     * @var string
     */
    private $url;

    /**
     * GET-ben kuldendo adatokat tartalmazo asszociativ tomb.
     *
     * @var array
     */
    private $getData = array();

    /**
     * POST-ben kuldendo adatokat tartalmazo asszociativ tomb.
     *
     * @var array
     */
    private $postData = array();

    /**
     * COOKIE-ben kuldendo adatokat tartalmazo asszociativ tomb.
     *
     * @var array
     */
    private $cookieData = array();

    /**
     * Kuldendo fileok adatait tartalmazo asszociativ tomb.
     *
     * @var array
     */
    private $fileData = array();

    /**
     * Egyeni headereket lehet beallitani a curl hivasnak
     *
     * @var array
     */
    private $customHeaderData = array();


    /** Post metodus */
    const METHOD_POST = 'post';
    /** Get metodus */
    const METHOD_GET = 'get';


    /**
     * Inicializalja a cURL kapcsolatot.
     *
     * @param string $url              A keres url-je.
     * @param string $method           Az adatkuldes metodusa {@USES self::METHOD_*}.
     * @param bool   $returnTransfer   Kerunk-e valaszt a fogadotol.
     * @param int    $timeOut          A keres max hossza masodpercekben.
     */
    public function __construct($url, $method = self::METHOD_GET, $returnTransfer = true, $timeOut = 1)
    {

        $this->handle = curl_init();
        $this->url = $url;

        if (strpos($url, 'https://') === 0) {
            $this->setSslVerifyMode(false);
        }

        switch ($method) {
            case self::METHOD_POST:
                curl_setopt($this->handle, CURLOPT_POST, true);
                break;
            case self::METHOD_GET:
            default:
                curl_setopt($this->handle, CURLOPT_HTTPGET, true);
        }

        if ($returnTransfer) {
            curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);
        }

        if ($timeOut > 0) {
            curl_setopt($this->handle, CURLOPT_TIMEOUT, $timeOut);
        }
    }

    /**
     * Felszabaditja az eroforrasokat.
     */
    public function __destruct()
    {
        if (is_resource($this->handle)) {
            curl_close($this->handle);
        }
    }

    /**
     * HTTPS hivas eseten beallitja hogy kell-e bizonyitvany ellenorzes.
     *
     * @param bool $mode   Legyen-e ellenorzes.
     *
     * @return Curl   Az objektum.
     */
    public function setSslVerifyMode($mode)
    {
        curl_setopt($this->handle, CURLOPT_SSL_VERIFYPEER, $mode);
        return $this;
    }

    /**
     * Beallitja a keres GET parametereit.
     *
     * @param array $getData   A parametereket tartalmazo tomb.
     *
     * @return Curl   Az objektum.
     */
    public function setGet(array $getData)
    {
        $this->getData = array_merge($this->getData, $getData);
        return $this;
    }

    /**
     * Beallitja a keres POST parametereit.
     *
     * @param array $postData   A kuldendo adatok.
     *
     * @return Curl   Az objektum.
     */
    public function setPost(array $postData)
    {
        $this->postData = array_merge($this->postData, $postData);
        return $this;
    }

    /**
     * Beallitja a keres COOKIE parametereit.
     *
     * @param array $cookieData   Cookie adatok tombben.
     *
     * @return Curl   Az objektum.
     */
    public function setCookie(array $cookieData)
    {
        $this->cookieData = array_merge($this->cookieData, $cookieData);
        return $this;
    }

    /**
     * Beallitja a keres egyene header parametereit.
     *
     * @param array $headerData   Header adatok tombben.
     *
     * @return Curl   Az objektum.
     */
    public function setCustomHeader(array $headerData)
    {
        $this->customHeaderData = array_merge($this->customHeaderData, $headerData);
        return $this;
    }

    /**
     * Beallitja a file-transzferhez szukseges parametereket a kereshez.
     *
     * @param array $postData   File atvitel mellett kuldendo adatok tombben.
     * @param array $fileData   File atvitelhez adatok tombben.
     *
     * @return Curl   Az objektum.
     */
    public function setFile(array $postData, array $fileData)
    {
        $this->postData = array_merge($this->postData, $postData);
        $this->fileData = $fileData;

        return $this;
    }

    /**
     * A lekeresrol ad vissza ainformaciokat.
     *
     * @return array   Asszociativ tomb amiben az informaciok vannak.
     */
    public function getInfo()
    {
        return curl_getinfo($this->handle);
    }

    /**
     * Elkuldi a kerest.
     *
     * @return mixed   TRUE, vagy FALSE, a keres sikeressegetol fuggoen, illetve ReturnTransfer mod eseten
     *                 a lekerdezett tartalom (sztring), vagy hiba eseten FALSE.
     */
    public function exec()
    {

        if (!empty($this->getData)) {
            $this->url .= (strpos($this->url, '?') === false ? '?' : '&').http_build_query($this->getData, null, '&');
        }
        if (!empty($this->postData)) {
            // Fajlkuldesnel nem szabad atalakitani a POST adatokat, mert nem fogja kikuldeni a faljt. [Gixx]
            if (empty($this->fileData)) {
                curl_setopt($this->handle, CURLOPT_POSTFIELDS, http_build_query($this->postData, null, '&'));
            }
            else {
                curl_setopt($this->handle, CURLOPT_POSTFIELDS, $this->postData);
            }
        }
        if (!empty($this->cookieData)) {
            curl_setopt($this->handle, CURLOPT_COOKIE, http_build_query($this->cookieData, null, ';'));
        }
        if (!empty($this->fileData)) {
            curl_setopt($this->handle, CURLOPT_INFILESIZE, $this->fileData['file_size']);
            curl_setopt($this->handle, CURLOPT_INFILE, $this->fileData['resource']);
        }

        if(!empty($this->customHeaderData)){
            curl_setopt($this->handle, CURLOPT_HTTPHEADER, $this->customHeaderData);
        }

        curl_setopt($this->handle, CURLOPT_URL, $this->url);
        // Merjuk a vegrehajtas idejet
        $startTime = microtime(true);

        // Elkuldjuk a kerest
        $result = curl_exec($this->handle);

        // Ha volt hiba a kuldes soran akkor dobunk egy kivetelt
        if (curl_errno($this->handle) > 0) {
            throw new CurlException('Hiba a cURL kuldes soran: ' . curl_error($this->handle));
        }

        return $result;
    }
}