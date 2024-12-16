<?php

namespace PaymentSystem\Laravel\Messages;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;
use EventSauce\EventSourcing\MessageDispatcher;

readonly class CollectingMessageDispatcher implements MessageDispatcher
{
    /**
     * @param iterable<MessageConsumer> $consumers
     */
    public function __construct(
        private iterable $consumers,
    ) {
    }

    public function dispatch(Message ...$messages): void
    {
        foreach ($messages as $message) {
            foreach ($this->consumers as $consumer) {
                $consumer->handle($message);
            }
        }
    }
}