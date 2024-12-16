<?php

namespace PaymentSystem\Laravel\Jobs;

use EventSauce\EventSourcing\AggregateRootId;
use PaymentSystem\Repositories\PaymentIntentRepositoryInterface;

readonly class CancelPaymentIntentJob
{
    public function __construct(private AggregateRootId $paymentIntentId)
    {
    }

    public function __invoke(PaymentIntentRepositoryInterface $paymentIntentRepository): void
    {
        $paymentIntent = $paymentIntentRepository->retrieve($this->paymentIntentId)->cancel();

        $paymentIntentRepository->persist($paymentIntent);
    }
}