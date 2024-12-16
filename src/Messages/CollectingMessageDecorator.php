<?php

namespace PaymentSystem\Laravel\Messages;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDecorator;

readonly class CollectingMessageDecorator implements MessageDecorator
{
    /**
     * @param iterable<MessageDecorator> $decorators
     */
    public function __construct(
        private iterable $decorators
    ) {
    }

    public function decorate(Message $message): Message
    {
        foreach ($this->decorators as $decorator) {
            $message = $decorator->decorate($message);
        }

        return $message;
    }
}