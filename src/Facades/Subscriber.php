<?php

namespace Daalvand\PubSub\Facades;


use Closure;
use Daalvand\PubSub\Contracts\Streamer;
use Daalvand\PubSub\Subscriber as BaseSubscriber;
use Illuminate\Support\Facades\Facade;

/**
 * Class Subscriber
 * @method  static BaseSubscriber setStreamer(Streamer $streamer)
 * @method  static void subscribe(array|string $channels, Closure $closure, string $microservice = null)
 * @package Daalvand\PubSub\Facades
 */
class Subscriber extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return BaseSubscriber::class;
    }
}