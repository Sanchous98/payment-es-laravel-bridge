<?php

namespace PaymentSystem\Laravel\Jobs;

use EventSauce\EventSourcing\AggregateRootId;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PaymentSystem\Laravel\Contracts\AccountablePaymentIntentRepository;
use PaymentSystem\Laravel\Models\Account;

class CancelPaymentIntentJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;
    use SerializesModels;
    use InteractsWithQueue;

    public function __construct(
        private readonly AggregateRootId $id,
        private readonly Account $account,
    ) {
    }

    public function uniqueId(): string
    {
        return $this->id;
    }

    public function __invoke(AccountablePaymentIntentRepository $repository): void
    {
        $repository
            ->forAccount($this->account)
            ->persist($repository->retrieve($this->id)->cancel());
    }
}