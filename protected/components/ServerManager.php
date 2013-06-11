<?php

class ServerManager
{
    private static $instance = null;
    private $runningModes = array('S', 'R', 'W');
    private $processList = array();
    private static $CURL_OPTS = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_USERAGENT      => 'hsch-1.0',
        CURLOPT_PORT           => 80,
        //CURLOPT_PROXY          => 'proxy.farm.publishing.hu:8080',
    );

    private function __construct() {}

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new ServerManager();
        }
        return self::$instance;
    }

    /**
     * A megadott nevű action-t megkeresi a futó processek között
     * Az Apache ServerStatus modul telepítve kell hogy legyen!
     *
     * @param string $action a process (általában az action) neve, nem szabad / jellel kezdeni!
     * @param boolean $rereadProcessList szűrés előtt kiolvassa-e a process listát. Egyszer muszáj, de a későbbi szűrésekben ez megspórolható, ha nem tellik el túl sok idő a hívások között
     */
    public function getRunningProcessCount($action, $rereadProcessList = true)
    {
        if ($rereadProcessList) $this->getProcessList();
        $match = $this->filterProcessList(array(
        	'M' => implode('|',$this->runningModes),
        	'Request' => '\/'.$action.' ',
        ));
        return count($match);
    }

    /**
     * Megvizsgálja, hogy megadott nevű exclusive csoportból csak 1 process fut-e (azaz önmaga)
     * Alapértelmezés szerint véletlenszerű idő múlva még egyszer próbálkozik.
     *
     * @param string $exclusive_group_name a config-ban az exclusiveTasks alatti kulcsok közül az egyik
     * @param boolean $retry szabad-e újrapróbálkozni, default: true
     */
    public function isExclusive($exclusive_group_name, $retry = true)
    {
        $process_names = Yii::app()->params['exclusiveTasks'][$exclusive_group_name];
        if (empty($process_names)) throw new Exception('Invalid exclusive group name: '.$exclusive_group_name);

        if ($this->getRunningProcessesCount($process_names) <= 1) return true;

        if ($retry) {
            // véletlenszerű várakozás
            $wait = rand(10, 50);
            sleep($wait);

            if ($this->getRunningProcessesCount($process_names) <= 1) return true;
        }
        return false;
    }

    /**
     * Az átadott process-listából hány példány fut összesen
     * Csak egyszer olvassa ki a futó processzek listáját
     *
     * @param array $process_names
     */
    private function getRunningProcessesCount($process_names)
    {
        $process_instances = 0;

        $this->getProcessList();
        foreach ($process_names as $process_name) {
            $process_instances += $this->getRunningProcessCount($process_name, false);
        }

        return $process_instances;
    }

    /**
     * Osztályváltozóba írja a process listát asszociatív tömb formában, a táblázat fejlécére kulcsolva
     *
     */
    private function getProcessList()
    {
        $response_html = $this->makeRequest(Yii::app()->params['server-status'], array());

        preg_match('/\<table(.*?)\<\/table\>/s', $response_html, $status_table);
        $status_table = $status_table[0];
        preg_match_all('/\<th\>([^<]*)\<\/th\>/s', $status_table, $headers);

        // összegyűjtjük a táblézat fejlécét
        $header = array();
        foreach ($headers[1] as $h) {
            $header[] = trim($h, "\n");
        }

        // sorokra tördelés
        preg_match_all('/\<tr\>(.*?)\<\/tr\>/s', $status_table, $rows);
        $data_rows = $rows[1];
        array_shift($data_rows);

        $this->processList = array();
        $r = 0;
        foreach ($data_rows as $data_row) {
            preg_match_all('/\<td(?:[^<]*)\>(.*?)\<\/td\>/s', $data_row, $data);
            $n = 0;
            foreach ($data[1] as $field) {
                $fld = trim($field, "\n");
                if (substr($fld,0,3) == '<b>') {
                    preg_match('/\<b\>(.*?)\<\/b\>/',$fld,$field);
                    $fld = $field[1];
                }
                $this->processList[$r][$header[$n++]] = $fld;
            }
            $r++;
        }
//print_r($this->processList);
    }

    /**
     * Az osztályváltozóban levő process listát megszűri az átadott tömbnek megfelelően
     *
     * @param array $filter kulcs a process táblázat fejléce, érték a preg_match-nek átadható regexp, nyitó-záró /-ek nélkül
     */
    private function filterProcessList(array $filter)
    {
        $result = array();
        foreach ($this->processList as $process) {
            $ok = true;
            foreach ($filter as $field => $value) {
                $ok &= preg_match('/'.$value.'/', $process[$field]);
            }

            if ($ok) $result[] = $process;
        };

        return $result;
    }

    /**
    * Makes an HTTP request. This method can be overriden by subclasses if
    * developers want to do fancier things or use something other than curl to
    * make the request.
    *
    * @param String $url the URL to make the request to
    * @param Array $params the parameters to use for the POST body
    * @param CurlHandler $ch optional initialized curl handle
    * @return String the response text
    */
    protected function makeRequest($url, $params, $ch=null)
    {
        $private_channel = false;
        if (!$ch) {
          $ch = curl_init();
          $private_channel = true;
        }

        $opts = self::$CURL_OPTS;
        if (!empty($params)) $opts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
        $opts[CURLOPT_URL] = $url;

        // disable the 'Expect: 100-continue' behaviour. This causes CURL to wait
        // for 2 seconds if the server does not support this header.
        if (isset($opts[CURLOPT_HTTPHEADER])) {
          $existing_headers = $opts[CURLOPT_HTTPHEADER];
          $existing_headers[] = 'Expect:';
          $opts[CURLOPT_HTTPHEADER] = $existing_headers;
        } else {
          $opts[CURLOPT_HTTPHEADER] = array('Expect:');
        }

        curl_setopt_array($ch, $opts);
        $result = curl_exec($ch);

        if ($result === false) {
          $e = new Exception(array(
            'error_code' => curl_errno($ch),
            'error'      => array(
              'message' => curl_error($ch),
              'type'    => 'CurlException',
            ),
          ));
          if ($private_channel) curl_close($ch);
          throw $e;
        }
        if ($private_channel) curl_close($ch);
        return $result;
    }

}