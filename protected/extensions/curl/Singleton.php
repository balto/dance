<?php

class Singleton
{
    /**
     * Singleton peldany.
     *
     * @var Singleton
     */
    protected static $instance;

    /**
     * Konstruktor.
     */
    protected function __construct()
    {
    }

    /**
     * Singleton magic __clone
     *
     * @return void
     */
    protected function __clone()
    {
    }

    /**
     * Visszaadja az objektum peldanyat.
     *
     * @return Singleton
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}
