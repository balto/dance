<?php

class CodecAdapterText extends CodecAdapterAbstract
{
    /**
     * Kodolja a megadott szoveget.
     *
     * @param string $text   A kodolando szoveg.
     */
    public function encode($text)
    {
        return $text;
    }

    /**
     * Dekodolja a megadott szoveget.
     *
     * @param string $text   A dekodolando szoveg.
     */
    public function decode($text)
    {
        return $text;
    }
}

