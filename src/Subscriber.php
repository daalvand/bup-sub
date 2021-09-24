<?php


namespace Daalvand\PubSub;


use Daalvand\PubSub\Contracts\Streamer;
use Daalvand\PubSub\Helper\Serializer;
use Closure;

class Subscriber
{
    private Streamer $streamer;
    private Closure $subscribeClosure;
    private string $env;

    /**
     * Subscriber constructor.
     * @param Streamer $streamer
     * @param string   $env
     */
    public function __construct(Streamer $streamer, string $env)
    {
        $this->streamer = $streamer;
        $this->env      = $env;
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
     * @param string[]|string $channels
     * @param Closure         $closure
     * @param string|null     $microservice
     */
    public function subscribe($channels, Closure $closure, string $microservice = null): void
    {
        $channels               = $this->parsChannelsName($channels, $microservice);
        $this->subscribeClosure = $closure;
        $this->streamer->subscribe($channels, Closure::fromCallable("static::parsData"));
    }

    protected function parsData(string $data): void
    {
        ($this->subscribeClosure)(Serializer::unserialize($data));
    }

    /**
     * @param string[]|string $channels
     * @param string|null     $microservice
     * @return string|string[]
     */
    private function parsChannelsName($channels, ?string $microservice)
    {
        $prefix = $microservice ? $microservice . '_' : '';
        $suffix = '_' . $this->env;
        foreach ($channels as $index => $channel) {
            $channels[$index] = $prefix.$channel.$suffix;
        }
        return $channels;
    }
}