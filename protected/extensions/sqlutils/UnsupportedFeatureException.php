<?php
class UnsupportedFeatureException extends Exception {

        protected $key;

        public function __construct($key) {
            $this->key = $key;
            parent::__construct($key . " not implemented.", 20);
        }

        public function getKey() {
            return $this->key;
        }
    }
?>
