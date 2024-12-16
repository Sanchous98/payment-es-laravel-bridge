<?php

namespace PaymentSystem\Laravel\Jobs;
;
use EventSauce\EventSourcing\AggregateRootId;
use Illuminate\Support\Str;
use Money\Money;
use PaymentSystem\Commands\AuthorizePaymentCommandInterface;
use PaymentSystem\Laravel\Uuid;
use PaymentSystem\PaymentIntentAggregateRoot;
use PaymentSystem\Repositories\PaymentIntentRepositoryInterface;
use PaymentSystem\TenderInterface;
use PaymentSystem\ValueObjects\ThreeDSResult;

readonly class AuthorizePaymentIntentJob implements AuthorizePaymentCommandInterface
{
    public function __construct(
        private AggregateRootId $id,
        private Money $money,
        private ?TenderInterface $tender = null,
        private string $merchantDescriptor = '',
        private string $description = '',
        private ?ThreeDSResult $threeDSResult = null,
    ) {}

    public function __invoke(PaymentIntentRepositoryInterface $repository): void
    {
        $paymentIntent = PaymentIntentAggregateRoot::authorize($this);

        $repository->persist($paymentIntent);
    }

    public function getId(): AggregateRootId
    {
        return $this->id;
    }

    public function getMoney(): Money
    {
        return $this->money;
    }

    public function getTender(): ?TenderInterface
    {
        return $this->tender;
    }

    public function getMerchantDescriptor(): string
    {
        return $this->merchantDescriptor;
    }

    public function getThreeDSResult(): ?ThreeDSResult
    {
        return $this->threeDSResult;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
