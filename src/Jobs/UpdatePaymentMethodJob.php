<?php

namespace PaymentSystem\Laravel\Jobs;

use EventSauce\EventSourcing\AggregateRootId;
use PaymentSystem\Commands\UpdatedPaymentMethodCommandInterface;
use PaymentSystem\Repositories\PaymentMethodRepositoryInterface;
use PaymentSystem\ValueObjects\BillingAddress;

readonly class UpdatePaymentMethodJob implements UpdatedPaymentMethodCommandInterface
{
    public function __construct(
        private AggregateRootId $paymentMethodId,
        private BillingAddress $billingAddress,
    ) {
    }

    public function __invoke(PaymentMethodRepositoryInterface $repository): void
    {
        $paymentMethod = $repository->retrieve($this->paymentMethodId)->update($this);

        $repository->persist($paymentMethod);
    }

    public function getBillingAddress(): BillingAddress
    {
        return $this->billingAddress;
    }
}
