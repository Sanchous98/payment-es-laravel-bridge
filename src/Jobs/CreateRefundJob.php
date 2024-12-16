<?php

namespace PaymentSystem\Laravel\Jobs;

use EventSauce\EventSourcing\AggregateRootId;
use Money\Money;
use PaymentSystem\Commands\CreateRefundCommandInterface;
use PaymentSystem\PaymentIntentAggregateRoot;
use PaymentSystem\RefundAggregateRoot;
use PaymentSystem\Repositories\RefundRepositoryInterface;

readonly class CreateRefundJob implements CreateRefundCommandInterface
{
    public function __construct(
        private AggregateRootId $id,
        private PaymentIntentAggregateRoot $paymentIntent,
        private Money $money,
    ) {
    }

    public function __invoke(RefundRepositoryInterface $repository): void
    {
        $refund = RefundAggregateRoot::create($this);

        $repository->persist($refund);
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