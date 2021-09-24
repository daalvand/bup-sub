<?php


namespace Daalvand\PubSub;


use Daalvand\PubSub\Concretes\KafkaStreamer;
use Daalvand\PubSub\Concretes\RedisStreamer;
use Daalvand\PubSub\Contracts\Streamer;
use Daalvand\PubSub\Exceptions\InvalidDriverException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class PubSubServiceProvider extends BaseServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/configs/pub-sub.php' => base_path("config/pub-sub.php"),
        ]);
    }


    public function register(): void
    {
        $this->app->bind(Subscriber::class, function (Container $app) {
            return new Subscriber($app->make(Streamer::class), config('app.env'));
        });

        $this->app->bind(Publisher::class, function (Container $app) {
            $microservice = $this->getMicroservice($app);
            return new Publisher($app->make(Streamer::class), $microservice, config('app.env'));
        });

        $this->app->bind(Streamer::class, function (Container $app) {
            return $this->resolveStreamer($app);
        });
    }

    /**
     * @param Container $app
     * @return Streamer
     * @throws InvalidDriverException|BindingResolutionException
     */
    protected function resolveStreamer(Container $app)
    {
        $driver = $app->make('config')->get('pub-sub.default');
        switch ($driver) {
            case 'kafka':
                return $this->resolveKafka($app);
            case 'redis':
                return $this->resolveRedisStreamer($app);
            default:
                throw new InvalidDriverException($driver);
        }
    }

    /**
     * @param Container $app
     * @return KafkaStreamer
     * @throws BindingResolutionException
     */
    protected function resolveKafka(Container $app): KafkaStreamer
    {
        $brokers      = $app->make('config')->get('pub-sub.drivers.kafka.brokers');
        $microservice = $app->make('config')->get('app.microservice_name');
        return new KafkaStreamer($brokers, $microservice);
    }

    /**
     * @param Container $app
     * @return RedisStreamer
     * @throws BindingResolutionException
     */
    protected function resolveRedisStreamer(Container $app): RedisStreamer
    {
        return new RedisStreamer($app->make('redis'));
    }

    /**
     * @param Container $app
     * @return mixed
     * @throws BindingResolutionException
     */
    protected function getMicroservice(Container $app):string
    {
        return $app->make('config')->get('app.microservice_name');
    }


}