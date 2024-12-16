<?php

namespace PaymentSystem\Laravel\Messages;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;
use Illuminate\Events\Dispatcher;

readonly class EventDispatcherAdapter implements MessageConsumer
{
    public function __construct(
        private Dispatcher $eventDispatcher,
    ) {
    }

    public function handle(Message $message): void
    {
        $this->eventDispatcher->dispatch($message->payload()::class, $message);
    }
}
