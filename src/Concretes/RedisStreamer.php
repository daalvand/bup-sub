<?php


namespace Daalvand\PubSub\Concretes;


use Daalvand\PubSub\Contracts\Streamer;
use Daalvand\PubSub\Exceptions\PublishException;
use Closure;
use Illuminate\Redis\RedisManager;
use Throwable;

class RedisStreamer implements Streamer
{
    private RedisManager $service;

    public function __construct(RedisManager $redisManager)
    {
        $this->service = $redisManager;
    }

    /**
     * @param string $channel
     * @param string $body
     * @throws PublishException
     */
    public function publish(string $channel, string $body): void
    {
        try {
            $this->service->publish($channel, $body);
        } catch (Throwable $e) {
            throw new PublishException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array|string $channels
     * @param \Closure     $closure (string $body, array $headers)
     */
    public function subscribe($channels, Closure $closure): void
    {
        $this->service->subscribe($channels, $closure);
    }

}