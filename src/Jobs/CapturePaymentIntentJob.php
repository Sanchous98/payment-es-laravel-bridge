<?php

namespace PaymentSystem\Laravel\Jobs;

use EventSauce\EventSourcing\AggregateRootId;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use PaymentSystem\Commands\CapturePaymentCommandInterface;
use PaymentSystem\Laravel\Messages\AccountContextDispatcher;
use PaymentSystem\Laravel\Models\Account;
use PaymentSystem\Repositories\PaymentIntentRepositoryInterface;
use PaymentSystem\TenderInterface;

class CapturePaymentIntentJob implements CapturePaymentCommandInterface, ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function __construct(
        private readonly AggregateRootId $id,
        private readonly Account $account,
        private readonly ?string $amount = null,
        private readonly ?TenderInterface $tender = null,
    ) {
    }

    public function uniqueId(): string
    {
        return $this->id;
    }

    public function __invoke(PaymentIntentRepositoryInterface $repository, AccountContextDispatcher $context): void
    {
        $context->forAccounts($this->account)
            ->run(fn() => $repository->persist($repository->retrieve($this->id)->capture($this)));
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