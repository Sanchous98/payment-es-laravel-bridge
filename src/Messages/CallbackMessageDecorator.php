<?php

namespace PaymentSystem\Laravel\Messages;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDecorator;

class CallbackMessageDecorator implements MessageDecorator
{
    /** @param callable(Message): Message $callback */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function decorate(Message $message): Message
    {
        return ($this->callback)($message);
    }
}