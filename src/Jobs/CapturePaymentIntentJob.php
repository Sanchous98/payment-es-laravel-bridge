<?php

namespace PaymentSystem\Laravel\Jobs;

use EventSauce\EventSourcing\AggregateRootId;
use PaymentSystem\Commands\CapturePaymentCommandInterface;
use PaymentSystem\Repositories\PaymentIntentRepositoryInterface;
use PaymentSystem\TenderInterface;

readonly class CapturePaymentIntentJob implements CapturePaymentCommandInterface
{
    public function __construct(
        private AggregateRootId $aggregateRootId,
        private ?string $amount = null,
        private ?TenderInterface $tender = null,
    ) {
    }

    public function __invoke(PaymentIntentRepositoryInterface $paymentIntentRepository): void
    {
        $paymentIntent = $paymentIntentRepository->retrieve($this->aggregateRootId)->capture($this);

        $paymentIntentRepository->persist($paymentIntent);
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function getTender(): ?TenderInterface
    {
        return $this->tender;
    }
}