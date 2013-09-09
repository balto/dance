<?php


class CodecAdapterJson extends CodecAdapterAbstract
{
    /**
     * Kodolja a megadott szoveget.
     *
     * @param string $text   A kodolando szoveg.
     */
    public function encode($text)
    {
        return json_encode($text);
    }

    /**
     * Dekodolja a megadott szoveget.
     *
     * @param string $text   A dekodolando szoveg.
     */
    public function decode($text)
    {
        return json_decode($text, true);
    }
}
