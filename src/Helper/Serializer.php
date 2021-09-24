<?php


namespace Daalvand\PubSub\Helper;


use Daalvand\PubSub\Message;
use function Opis\Closure\serialize as OpisSerializer;
use function Opis\Closure\unserialize as OpisUnserializer;

class Serializer
{
    public static function serialize(Message $data): string
    {
        return json_encode(['data' => OpisSerializer($data)]);
    }

    public static function unserialize(string $payload): Message
    {
        $array = json_decode($payload, true);
        return OpisUnserializer($array['data']);
    }
}