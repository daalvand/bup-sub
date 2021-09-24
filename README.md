# PUB SUB

* This package used for publish and subscribe with redis or kafka

# installation
## install package
run `composer require daalvand/pubsub`

### publish provider

#### Laravel
* `php artisan vendor:publish --provider="Daalvand\PubSub\PubSubServiceProvider"`

#### Lumen
* Add the service provider to `bootstrap/app.php` file:
```php
<?php
//...
/** @var App $app */
$app->register(Daalvand\PubSub\PubSubServiceProvider::class);
```

Copy the config files from `/vendor/daalvand/pubsub/src/config` to `config` directory. Then configure it in  `/bootstrap/app.php` file:

```php
<?php
/** @var App $app */
$app->configure("pub-sub");
```

## USAGE

#### Publisher

```php


use Daalvand\PubSub\Facades\Publisher;

Publisher::publish('channel_name', $data);

```

#### Subscriber

```php

use Daalvand\PubSub\Facades\Subscriber;
use Daalvand\PubSub\Message;

Subscriber::subscribe(['channel_one', 'channel_two'], function (Message $message) {
    switch ($message->getType()) {
    case 'channel_one':
        //a
        break;
    case 'channel_two':
        //b
        break;
    }
}, 'user');

```
**NOTE**

Notice that the real channel name is like below:
`{microservice_name}_{channel_name}_{environment}`
