<?php

namespace PaymentSystem\Laravel\Jobs;

use EventSauce\EventSourcing\AggregateRootId;
use Illuminate\Support\Str;
use PaymentSystem\Commands\CreateTokenPaymentMethodCommandInterface;
use PaymentSystem\Laravel\Uuid;
use PaymentSystem\PaymentMethodAggregateRoot;
use PaymentSystem\Repositories\PaymentMethodRepositoryInterface;
use PaymentSystem\TokenAggregateRoot;
use PaymentSystem\ValueObjects\BillingAddress;

readonly class CreateTokenPaymentMethodJob implements CreateTokenPaymentMethodCommandInterface
{
    public function __construct(
        private AggregateRootId $id,
        private BillingAddress $billingAddress,
        private TokenAggregateRoot $token,
    ) {
    }

    public function __invoke(PaymentMethodRepositoryInterface $paymentMethodRepository): void
    {
        $paymentMethod = PaymentMethodAggregateRoot::createFromToken($this);

        $paymentMethodRepository->persist($paymentMethod);
    }

    public function getId(): AggregateRootId
    {
        return $this->id;
    }

    public function getBillingAddress(): BillingAddress
    {
        return $this->billingAddress;
    }

    public function getToken(): TokenAggregateRoot
    {
        return $this->token;
    }
}