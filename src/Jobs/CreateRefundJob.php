<?php

namespace PaymentSystem\Laravel\Jobs;

use EventSauce\EventSourcing\AggregateRootId;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Money\Money;
use PaymentSystem\Commands\CreateRefundCommandInterface;
use PaymentSystem\Laravel\Messages\AccountContextDispatcher;
use PaymentSystem\Laravel\Models\Account;
use PaymentSystem\PaymentIntentAggregateRoot;
use PaymentSystem\RefundAggregateRoot;
use PaymentSystem\Repositories\RefundRepositoryInterface;

class CreateRefundJob implements CreateRefundCommandInterface, ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function __construct(
        private readonly AggregateRootId $id,
        private readonly PaymentIntentAggregateRoot $paymentIntent,
        private readonly Money $money,
        private readonly Account $account,
    ) {
    }

    public function uniqueId(): string
    {
        return $this->id;
    }

    public function __invoke(RefundRepositoryInterface $repository, AccountContextDispatcher $context): void
    {
        $context->forAccounts($this->account)
            ->run(fn() => $repository->persist(RefundAggregateRoot::create($this)));
    }

    public function getId(): AggregateRootId
    {
        return $this->id;
    }

    public function getPaymentIntent(): PaymentIntentAggregateRoot
    {
        return $this->paymentIntent;
    }

    public function getMoney(): Money
    {
        return $this->money;
    }
}