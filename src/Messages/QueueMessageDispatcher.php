<?php

namespace PaymentSystem\Laravel\Messages;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher;
use Illuminate\Contracts\Bus\Dispatcher;

readonly class QueueMessageDispatcher implements MessageDispatcher
{
    /**
     * @param iterable<MessageDispatcher> $dispatchers
     */
    public function __construct(
        private iterable $dispatchers,
        private Dispatcher $dispatcher,
    ) {
    }

    public function dispatch(Message ...$messages): void
    {
        foreach ($this->dispatchers as $dispatcher) {
            $this->dispatcher->dispatch(new QueueMessagesJob($dispatcher, ...$messages));
        }
    }
}