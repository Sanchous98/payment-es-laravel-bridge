<?php

namespace PaymentSystem\Laravel\Repository;

use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\ClassNameInflector;
use EventSauce\EventSourcing\EventSourcedAggregateRootRepository;
use EventSauce\EventSourcing\MessageDecorator;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Snapshotting\AggregateRootRepositoryWithSnapshotting;
use PaymentSystem\DisputeAggregateRoot;
use PaymentSystem\Repositories\DisputeRepositoryInterface;

/**
 * @extends EventSourcedAggregateRootRepository<DisputeAggregateRoot>
 */
class DisputeRepository extends EventSourcedAggregateRootRepository implements AggregateRootRepositoryWithSnapshotting, DisputeRepositoryInterface
{
    use SnapshotBehavior;

    public function __construct(
        MessageRepository $messageRepository,
        ?MessageDispatcher $dispatcher = null,
        ?MessageDecorator $decorator = null,
        ?ClassNameInflector $classNameInflector = null
    ) {
        $this->messageRepository = $messageRepository;
        $this->className = DisputeAggregateRoot::class;

        parent::__construct(DisputeAggregateRoot::class, $messageRepository, $dispatcher, $decorator, $classNameInflector);
    }

    public function retrieve(AggregateRootId $aggregateRootId): DisputeAggregateRoot
    {
        return parent::retrieve($aggregateRootId);
    }
}
