<?php

namespace Daalvand\PubSub\Facades;


use Daalvand\PubSub\Contracts\Streamer;
use Daalvand\PubSub\Publisher as BasePublisher;
use Illuminate\Support\Facades\Facade;

/**
 * Class Subscriber
 * @method  static BasePublisher setStreamer(Streamer $streamer)
 * @method  static void publish(string $type, array|string $data)
 * @package Daalvand\PubSub\Facades
 */
class Publisher extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return BasePublisher::class;
    }
}