<?php


namespace Daalvand\PubSub\Exceptions;


use Exception;

class InvalidDriverException extends Exception
{
    public function __construct($driver)
    {
        $driver = (string)$driver;
        parent::__construct("The driver $driver is invalid.");
    }
}