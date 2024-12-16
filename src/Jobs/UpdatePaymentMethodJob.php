<?php

namespace PaymentSystem\Laravel\Jobs;

use EventSauce\EventSourcing\AggregateRootId;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Support\Collection;
use PaymentSystem\Commands\UpdatedPaymentMethodCommandInterface;
use PaymentSystem\Laravel\Messages\AccountContextDispatcher;
use PaymentSystem\Laravel\Models\Account;
use PaymentSystem\Repositories\PaymentMethodRepositoryInterface;
use PaymentSystem\ValueObjects\BillingAddress;

class UpdatePaymentMethodJob implements UpdatedPaymentMethodCommandInterface, ShouldQueue, ShouldBeUnique
{
    use Queueable;

    private readonly Collection $accounts;

    public function __construct(
        private readonly AggregateRootId $id,
        private readonly BillingAddress $billingAddress,
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

    public function __invoke(PaymentMethodRepositoryInterface $repository, AccountContextDispatcher $context): void
    {
        $context->forAccounts(...$this->accounts)
            ->run(fn() => $repository->persist($repository->retrieve($this->id)->update($this)));
    }

    public function getBillingAddress(): BillingAddress
    {
        return $this->billingAddress;
    }
}
