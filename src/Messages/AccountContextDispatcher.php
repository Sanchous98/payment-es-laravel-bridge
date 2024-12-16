<?php

namespace PaymentSystem\Laravel\Messages;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher;
use InvalidArgumentException;
use PaymentSystem\Laravel\Models\Account;

class AccountContextDispatcher implements MessageDispatcher
{
    public const ACCOUNT_ID_HEADER = 'x-account-id';

    /**
     * @var Account[]
     */
    private array $accounts = [];

    public function __construct(
        private readonly MessageDispatcher $dispatcher,
    ) {
    }

    public function forAccounts(Account ...$accounts): static
    {
        $this->accounts = $accounts;

        return $this;
    }

    public function run(callable $callback): void
    {
        try {
            $callback();
        } finally {
            $this->accounts = [];
        }
    }

    public function dispatch(Message ...$messages): void
    {
        if (empty($this->accounts)) {
            throw new InvalidArgumentException('No accounts set to execute');
        }

        foreach ($messages as $message) {
            foreach ($this->accounts as $account) {
                $this->dispatcher->dispatch($message->withHeader(self::ACCOUNT_ID_HEADER, $account->id));
            }
        }
    }
}