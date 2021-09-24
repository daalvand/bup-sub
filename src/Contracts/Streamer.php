<?php


namespace Daalvand\PubSub\Contracts;


use Closure;

interface Streamer
{
    public function publish(string $channel, string $body): void;

    /**
     * @param array|string $channels
     * @param Closure      $closure
     */
    public function subscribe($channels, Closure $closure): void;
}