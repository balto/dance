<?php

class TimeManager
{
    private static $instance = null;
    private $times = array();

    private function __construct()
    {
        $this->start();
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new TimeManager();
        }
        return self::$instance;
    }

    private function getTime() {
        return microtime(true);
    }

    private function saveTimestamp($name) {
        $this->times[$name] = $this->getTime();
    }

    private function start() {
        $this->saveTimestamp('start');
    }

    public function responseSent() {
        $this->saveTimestamp('response');
    }

    public function end() {
        $this->saveTimestamp('end');

        if (YII_DEBUG) {
            $stat = $this->getStatistics();
            echo sprintf('<pre>Response: %f s, Postprocess: %f s</pre>', $stat['response'], $stat['postprocess']);
        }
    }

    public function getStatistics() {
        if (!isset($this->times['start']) ||
            !isset($this->times['response']) ||
            !isset($this->times['end']))
           return array();

        return array(
            'response' => $this->times['response'] - $this->times['start'],
            'postprocess' => $this->times['end'] - $this->times['response'],
        );
    }

    public function hasTimeToContinue($job)
    {
        if (!isset($this->times['start'])) return true;
        if (!isset(Yii::app()->params['max_time_for_'.$job])) return true;

        $now = $this->getTime();
        return ($this->times['start'] + Yii::app()->params['max_time_for_'.$job]) > $now;
    }

    public function dump() {
        echo $this->getTime()-$this->times['start']."<br />";
    }
}