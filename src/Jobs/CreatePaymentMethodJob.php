<?php

namespace PaymentSystem\Laravel\Jobs;

use EventSauce\EventSourcing\AggregateRootId;
use Illuminate\Support\Str;
use PaymentSystem\Commands\CreatePaymentMethodCommandInterface;
use PaymentSystem\Contracts\SourceInterface;
use PaymentSystem\Laravel\Uuid;
use PaymentSystem\PaymentMethodAggregateRoot;
use PaymentSystem\Repositories\PaymentMethodRepositoryInterface;
use PaymentSystem\ValueObjects\BillingAddress;

readonly class CreatePaymentMethodJob implements CreatePaymentMethodCommandInterface
{
    public function __construct(
        private AggregateRootId $id,
        private BillingAddress $billingAddress,
        private SourceInterface $source,
    ) {
    }

    public function __invoke(PaymentMethodRepositoryInterface $repository): void
    {
        $paymentMethod = PaymentMethodAggregateRoot::create($this);

        $repository->persist($paymentMethod);
    }

    public function getId(): AggregateRootId
    {
        return $this->id;
    }

    public function getBillingAddress(): BillingAddress
    {
        return $this->billingAddress;
    }

    public function getSource(): SourceInterface
    {
        return $this->source;
    }
}
