<?php

    /**
     * This class implements some helper functions.
     * @author arothe
     *
     */
    class PHPSQLParserUtils extends PHPSQLParserConstants {

        /**
         * Prints an array only if debug mode is on.
         * @param array $s
         * @param boolean $return, if true, the formatted array is returned via return parameter
         */
        protected function preprint($arr, $return = false) {
            $x = "<pre>";
            $x .= print_r($arr, 1);
            $x .= "</pre>";
            if ($return) {
                return $x;
            } else {
                if (isset($_ENV['DEBUG'])) {
                    print $x . "\n";
                }
            }
        }

        /**
         * 
         * Change the case of the values of an array.
         * 
         * @param Array of Strings $arr
         * @param unknown_type $case (CASE_LOWER or CASE_UPPER)
         * @throws InvalidArgumentException if the first argument is not an array
         */
        protected function changeCaseForArrayValues($arr, $case) {
            if (!is_array($arr)) {
                throw new InvalidArgumentException("first argument must be an array");
            }
            for ($i = 0; $i < count($arr); ++$i) {
                if ($case == CASE_LOWER) {
                    $arr[$i] = strtolower($arr[$i]);
                }
                if ($case == CASE_UPPER) {
                    $arr[$i] = strtoupper($arr[$i]);
                }
            }
            return $arr;
        }

        /**
         * Starts one of the strings within the given array $haystack with the string $needle?
         * @param string or array $haystack
         * @param string $needle
         */
        protected function startsWith($haystack, $needle) {
            if (is_string($needle)) {
                $needle = array($needle);
            }
            for ($j = 0; $j < count($needle); ++$j) {
                $length = strlen($needle[$j]);
                if (substr($haystack, 0, $length) === $needle[$j]) {
                    return $j;
                }
            }
            return false;
        }

        /**
         * Ends the given string $haystack with the string $needle?
         * @param string $haystack
         * @param string $needle
         */
        protected function endsWith($haystack, $needle) {
            $length = strlen($needle);
            if ($length == 0) {
                return true;
            }

            $start = $length * -1;
            return (substr($haystack, $start) === $needle);
        }
    }
?>
