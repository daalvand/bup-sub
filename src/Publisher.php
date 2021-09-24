<?php


namespace Daalvand\PubSub;


use Daalvand\PubSub\Contracts\Streamer;
use Daalvand\PubSub\Helper\Serializer;

class Publisher
{
    private Streamer $streamer;
    private string $microservice;
    private string $env;

    public function __construct(Streamer $streamer, string $microservice, string $env)
    {
        $this->streamer     = $streamer;
        $this->microservice = $microservice;
        $this->env          = $env;
    }

    /**
     * @param Streamer $streamer
     * @return $this
     */
    public function setStreamer(Streamer $streamer): self
    {
        $this->streamer = $streamer;
        return $this;
    }

    /**
     * data must be serializable
     * @param string $type
     * @param array|string $data
     * @return void
     */
    public function publish(string $type, $data): void
    {
        $this->streamer->publish($this->createChannelName($type), $this->prepareData($data, $type));
    }

    /**
     * @param        $data
     * @param string $type
     * @return string
     */
    private function prepareData($data, string $type): string
    {
        return Serializer::serialize(new Message($data, $type, $this->microservice));
    }

    /**
     * @param string $type
     * @return string
     */
    protected function createChannelName(string $type): string
    {
        return $this->microservice . '_' . $type . '_' . $this->env;
    }
}