<?php

namespace PaymentSystem\Laravel\Jobs;

use EventSauce\EventSourcing\AggregateRootId;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use PaymentSystem\Laravel\Messages\AccountContextDispatcher;
use PaymentSystem\Laravel\Models\Account;
use PaymentSystem\Repositories\PaymentIntentRepositoryInterface;

class CancelPaymentIntentJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function __construct(
        private readonly AggregateRootId $id,
        private readonly Account $account,
    ) {
    }

    public function uniqueId(): string
    {
        return $this->id;
    }

    public function __invoke(PaymentIntentRepositoryInterface $repository, AccountContextDispatcher $context): void
    {
        $context->forAccounts($this->account)
            ->run(fn() => $repository->persist($repository->retrieve($this->id)->cancel()));
    }
}