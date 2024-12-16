<?php

namespace PaymentSystem\Laravel\Repository;

use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\ClassNameInflector;
use EventSauce\EventSourcing\EventSourcedAggregateRootRepository;
use EventSauce\EventSourcing\MessageDecorator;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Snapshotting\AggregateRootRepositoryWithSnapshotting;
use PaymentSystem\PaymentIntentAggregateRoot;
use PaymentSystem\Repositories\PaymentIntentRepositoryInterface;

/**
 * @extends EventSourcedAggregateRootRepository<PaymentIntentAggregateRoot>
 */
class PaymentIntentRepository extends EventSourcedAggregateRootRepository implements AggregateRootRepositoryWithSnapshotting, PaymentIntentRepositoryInterface
{
    use SnapshotBehavior;

    public function __construct(
        MessageRepository $messageRepository,
        ?MessageDispatcher $dispatcher = null,
        ?MessageDecorator $decorator = null,
        ?ClassNameInflector $classNameInflector = null
    ) {
        $this->messageRepository = $messageRepository;
        $this->className = PaymentIntentAggregateRoot::class;

        parent::__construct(PaymentIntentAggregateRoot::class, $messageRepository, $dispatcher, $decorator, $classNameInflector);
    }

    public function retrieve(AggregateRootId $aggregateRootId): PaymentIntentAggregateRoot
    {
        return parent::retrieve($aggregateRootId);
    }
}
