<?php

/** @noinspection LaravelFunctionsInspection */
return [
    'default' => 'kafka',
    'drivers' => [
        'kafka' => [
            "brokers" => [
                env("KAFKA_PUB_SUB_HOST") . ":" . env("KAFKA_PUB_SUB_PORT"),
            ],
        ],
        'redis' => [
            //....
        ]
    ]
];