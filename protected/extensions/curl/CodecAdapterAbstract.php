<?php


abstract class CodecAdapterAbstract
{
    /**
     * Kodolja a megadott szoveget.
     *
     * @param string $text   A kodolando szoveg.
     */
    abstract public function encode($text);

    /**
     * Dekodolja a megadott szoveget.
     *
     * @param string $text   A dekodolando szoveg.
     */
    abstract public function decode($text);
}
