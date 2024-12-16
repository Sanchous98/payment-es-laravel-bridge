<?php

namespace PaymentSystem\Laravel\Jobs;

use EventSauce\EventSourcing\AggregateRootId;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Support\Collection;
use PaymentSystem\Commands\CreateTokenPaymentMethodCommandInterface;
use PaymentSystem\Laravel\Messages\AccountContextDispatcher;
use PaymentSystem\Laravel\Models\Account;
use PaymentSystem\PaymentMethodAggregateRoot;
use PaymentSystem\Repositories\PaymentMethodRepositoryInterface;
use PaymentSystem\TokenAggregateRoot;
use PaymentSystem\ValueObjects\BillingAddress;

class CreateTokenPaymentMethodJob implements CreateTokenPaymentMethodCommandInterface, ShouldQueue, ShouldBeUnique
{
    use Queueable;

    private readonly Collection $accounts;

    public function __construct(
        private readonly AggregateRootId $id,
        private readonly BillingAddress $billingAddress,
        private readonly TokenAggregateRoot $token,
        Account ...$accounts,
    ) {
        $accounts = collect($accounts);

        if ($accounts->isEmpty()) {
            $accounts = Account::query()->cursor();
        }

        if ($accounts->isEmpty()) {
            throw new RecordsNotFoundException('No accounts found.');
        }

        $this->accounts = collect($accounts);
    }

    public function uniqueId(): string
    {
        return $this->id;
    }

    public function __invoke(
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        AccountContextDispatcher $context
    ): void {
        $context->forAccounts(...$this->accounts)
            ->run(fn() => $paymentMethodRepository->persist(PaymentMethodAggregateRoot::createFromToken($this)));
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