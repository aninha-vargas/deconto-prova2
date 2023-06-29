<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    public function publish($message, $fila)
    {
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();
        $channel->exchange_declare('test_exchange', 'direct', false, false, false);
        $channel->queue_declare($fila, false, false, false, false);
        $channel->queue_bind($fila, 'test_exchange', 'test_key');
        $channel->basic_publish(
            new AMQPMessage(json_encode($message)),
            'test_exchange',
            'test_key'
        );
        // echo " [x] Sent $message to test_exchange / laravel.\n";
        $channel->close();
        $connection->close();
    }
    public function consume($fila)
    {
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();
        $messages = [];

        $callback = function ($msg) use (&$messages) {
            $messages[] = $msg->body;
        };
        $channel->queue_declare($fila, false, false, false, false);
        $channel->basic_consume($fila, '', false, true, false, false, $callback);

        while (count($messages) == 0) {
            $channel->wait();
            dd($messages);
        }

        $channel->close();
        $connection->close();

        return $messages;
    }
}
