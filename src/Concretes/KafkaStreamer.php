<?php


namespace Daalvand\PubSub\Concretes;


use Daalvand\PubSub\Contracts\Streamer;
use Daalvand\PubSub\Exceptions\PublishException;
use Daalvand\PubSub\Exceptions\SubscriptionException;
use Closure;
use Daalvand\Kafka\Consumer\ConsumerBuilder;
use Daalvand\Kafka\Consumer\ConsumerInterface;
use Daalvand\Kafka\Exceptions\ConsumerCommitException;
use Daalvand\Kafka\Exceptions\ConsumerConsumeException;
use Daalvand\Kafka\Exceptions\ConsumerEndOfPartitionException;
use Daalvand\Kafka\Exceptions\ConsumerSubscriptionException;
use Daalvand\Kafka\Exceptions\ConsumerTimeoutException;
use Daalvand\Kafka\Exceptions\ProducerException;
use Daalvand\Kafka\Message\ConsumerMessageInterface;
use Daalvand\Kafka\Message\ProducerMessage;
use Daalvand\Kafka\Producer\ProducerBuilder;
use Throwable;

class KafkaStreamer implements Streamer
{
    private array $brokers;
    private string $group;

    public function __construct(array $brokers, string $microservice)
    {
        $this->group   = $microservice;
        $this->brokers = $brokers;
    }

    /**
     * @param string $channel
     * @param string $body
     * @param array  $headers
     * @throws ProducerException|PublishException
     */
    public function publish(string $channel, string $body, array $headers = []): void
    {
        $producer        = (new ProducerBuilder())->setBrokers($this->brokers)->build();
        $producerMessage = (new ProducerMessage($channel, 0))
            ->withHeaders($headers)
            ->withBody($body);
        try {
            $producer->produce($producerMessage);
        } catch (Throwable $e) {
            throw new PublishException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array|string $channels
     * @param Closure  $closure (string $body, array $headers)
     */
    public function subscribe($channels, Closure $closure): void
    {
        $channels = is_array($channels) ? $channels : [$channels];
        $consumer = $this->startConsumer($channels);
        do {
            $consumerMessage = $this->consume($consumer);
            $body            = $consumerMessage->getBody();
            $headers         = $consumerMessage->getHeaders();
            $closure($body, $headers);
            $this->commit($consumer, $consumerMessage);
        } while (true);
    }


    /**
     * @param array $channels
     * @return ConsumerInterface
     * @throws SubscriptionException
     */
    private function startConsumer(array $channels): ConsumerInterface
    {
        try {
            $consumerBuilder = new ConsumerBuilder();
            foreach ($channels as $channel) {
                $consumerBuilder->withAdditionalSubscription($channel);
            }
            $consumer = $consumerBuilder
                ->withConsumerGroup($this->group)
                ->setBrokers($this->brokers)
                ->build();
            $consumer->subscribe();
        } catch (Throwable $e) {
            throw new SubscriptionException($e->getMessage(), $e->getCode(), $e);
        }
        return $consumer;
    }


    /**
     * @param ConsumerInterface $consumer
     * @return ConsumerMessageInterface
     * @throws SubscriptionException
     */
    private function consume(ConsumerInterface $consumer): ConsumerMessageInterface
    {
        try {
            $consumerMessage = $consumer->consume();
        } catch (ConsumerEndOfPartitionException | ConsumerTimeoutException $e) {
            return $this->consume($consumer);
        } catch (ConsumerConsumeException | ConsumerSubscriptionException $exception) {
            throw new SubscriptionException($exception->getMessage(), $exception->getCode(), $exception);
        }
        return $consumerMessage;
    }

    /**
     * @param ConsumerInterface        $consumer
     * @param ConsumerMessageInterface $consumerMessage
     * @throws SubscriptionException
     */
    private function commit(ConsumerInterface $consumer, ConsumerMessageInterface $consumerMessage): void
    {
        try {
            $consumer->commit($consumerMessage);
        } catch (ConsumerCommitException $e) {
            throw new SubscriptionException($e->getMessage(), $e->getCode(), $e);
        }
    }


}