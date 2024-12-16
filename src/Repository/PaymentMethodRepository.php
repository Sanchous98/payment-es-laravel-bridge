<?php

namespace PaymentSystem\Laravel\Repository;

use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\ClassNameInflector;
use EventSauce\EventSourcing\EventSourcedAggregateRootRepository;
use EventSauce\EventSourcing\MessageDecorator;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Snapshotting\AggregateRootRepositoryWithSnapshotting;
use PaymentSystem\PaymentMethodAggregateRoot;
use PaymentSystem\Repositories\PaymentMethodRepositoryInterface;

/**
 * @extends EventSourcedAggregateRootRepository<PaymentMethodAggregateRoot>
 */
class PaymentMethodRepository extends EventSourcedAggregateRootRepository implements AggregateRootRepositoryWithSnapshotting, PaymentMethodRepositoryInterface
{
    use SnapshotBehavior;

    public function __construct(
        MessageRepository $messageRepository,
        ?MessageDispatcher $dispatcher = null,
        ?MessageDecorator $decorator = null,
        ?ClassNameInflector $classNameInflector = null,
    ) {
        $this->messageRepository = $messageRepository;
        $this->className = PaymentMethodAggregateRoot::class;

        parent::__construct(PaymentMethodAggregateRoot::class, $messageRepository, $dispatcher, $decorator, $classNameInflector);
    }

    public function retrieve(AggregateRootId $aggregateRootId): PaymentMethodAggregateRoot
    {
        return parent::retrieve($aggregateRootId);
    }
}
