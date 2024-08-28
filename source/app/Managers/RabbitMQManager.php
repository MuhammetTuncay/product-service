<?php

namespace App\Managers;

use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQManager
{

    protected $connection = null;
    protected $channel;
    protected $exchange = '';

    public function __construct()
    {

    }

    /**
     * @throws \Exception
     */
    private function connection(): void
    {
        if ($this->connection === null) {
            $this->connection = new AMQPStreamConnection(
                config('queue.connections.rabbitmq.host'),
                config('queue.connections.rabbitmq.port'),
                config('queue.connections.rabbitmq.login'),
                config('queue.connections.rabbitmq.password'),
            );
            $this->channel = $this->connection->channel();
        }
    }

    public function send(string $queueName, $message, $exchange = '', $routing_key = ''): bool
    {
        try {

            $this->connection();

            if (empty($exchange)) {
                $exchange = 'amq.topic'; // Exchange ismi
                $queue = $queueName; // Kuyruk ismi
                $routing_key = ""; // Routing Key
                $exchangeType = 'topic';
                $this->channel->exchange_declare($exchange, $exchangeType, false, true, false);
                $this->channel->queue_declare($queue, false, true, false, false);
                $this->channel->queue_bind($queue, $exchange, $routing_key);
            }

            $message = new AMQPMessage($message);
            $this->channel->basic_publish($message, $exchange, $routing_key);

            return true;
        } catch (\Exception $e) {
            Log::critical("rabbitmq-exception", ['line' => __LINE__, 'message' => $e->getMessage()]);
            return false;
        }

    }

    public function publish(string $queue, mixed $message): bool
    {

        if (is_array($message)) {
            $message = json_encode($message, JSON_UNESCAPED_UNICODE);
        }

        try {
            $this->connection();
            list($queue, $messageCount, $consumerCount) = $this->channel->queue_declare($queue, false, true, false, false, false);
            $message_id = \Ramsey\Uuid\Rfc4122\UuidV5::uuid1()->toString();
            $properties = [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'message_id' => $message_id,
                'priority' => 1,
            ];
            $msg = new AMQPMessage($message, $properties);

            $this->channel->basic_publish($msg, $this->exchange, $queue);

        } catch (\Exception $e) {
            //sleep(3);
            Log::critical("rabbitmq-exception", ['line' => __LINE__, 'message' => $e->getMessage()]);
        }

        return true;
    }

    public function sendMessage($queue, $message, $exchange = '', $emptycheck = false, $notification = false, $priority = 1, $message_id = null)
    {
        list($queue, $messageCount, $consumerCount) = $this->channel->queue_declare($queue, false, true, false, false, false);

        $message_id = (empty($message_id)) ? \Ramsey\Uuid\Rfc4122\UuidV5::uuid1()->toString() : $message_id;
        $properties = [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            'message_id' => $message_id,
            'priority' => $priority,
        ];
        $msg = new AMQPMessage($message, $properties);

        $this->channel->basic_publish($msg, $exchange, $queue);

        return true;

    }

    public function addConsume($queue, $callback)
    {
        try {

            $this->connection();
            $this->channel->queue_declare($queue, false, true, false, false);
            $this->channel->basic_consume($queue, '', false, false, false, false, $callback);

        } catch (\Exception $e) {
            Log::critical("rabbitmq-exception", ['line' => __LINE__, 'message' => $e->getMessage()]);
        }
    }

    public function consume(): void
    {
        try {
            $this->connection();
            $this->channel->basic_qos(null, 1, true);
            while ($this->channel->is_consuming()) {
                $this->channel->wait();
            }
        } catch (\Exception $e) {
            //sleep(3);
            Log::critical("rabbitmq-exception", ['line' => __LINE__, 'message' => $e->getMessage()]);
        }
    }

    public function multiConsume(array $consume = []): void
    {
        try {
            foreach ($consume as $queue => $callback) {
                $this->addConsume($queue, $callback);
            }
            $this->consume();
        } catch (\Exception $e) {
            Log::critical("rabbitmq-exception", ['line' => __LINE__, 'message' => $e->getMessage()]);
        }
    }

    public function __destruct()
    {
        try {
            if ($this->connection !== null) {
                $this->connection->reconnect();
                $this->channel->close();
                $this->connection->close();
            }
        } catch (\Exception $e) {
            Log::critical("rabbitmq-exception", ['line' => __LINE__, 'message' => $e->getMessage()]);
        }
    }


}
