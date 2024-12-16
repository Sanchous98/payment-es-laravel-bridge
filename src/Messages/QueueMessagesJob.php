<?php

namespace PaymentSystem\Laravel\Messages;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher;

readonly class QueueMessagesJob
{
    private array $messages;

    public function __construct(
        private MessageDispatcher $dispatcher,
        Message ...$messages,
    ) {
        $this->messages = $messages;
    }

    public function __invoke(): void
    {
        $this->dispatcher->dispatch(...$this->messages);
    }
}