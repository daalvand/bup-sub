<?php


namespace Daalvand\PubSub;


class Message
{
    private        $data;
    private string $type;
    private string $microservice;

    /**
     * Message constructor.
     * @param        $data
     * @param string $type
     * @param string $microservice
     */
    public function __construct($data, string $type, string $microservice)
    {
        $this->data         = $data;
        $this->type         = $type;
        $this->microservice = $microservice;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getMicroservice(): string
    {
        return $this->microservice;
    }

}