<?php

namespace PaymentSystem\Laravel\Jobs;

use EventSauce\EventSourcing\AggregateRootId;
use PaymentSystem\Commands\CreateTokenCommandInterface;
use PaymentSystem\Repositories\TokenRepositoryInterface;
use PaymentSystem\TokenAggregateRoot;
use PaymentSystem\ValueObjects\CreditCard;

readonly class CreateTokenJob implements CreateTokenCommandInterface
{
    public function __construct(
        private AggregateRootId $id,
        private CreditCard $card,
    ) {
    }

    public function __invoke(TokenRepositoryInterface $repository): void
    {
        $token = TokenAggregateRoot::create($this);

        $repository->persist($token);
    }

    public function getId(): AggregateRootId
    {
        return $this->id;
    }

    public function getCard(): CreditCard
    {
        return $this->card;
    }
}