<?php

namespace PaymentSystem\Laravel\Repository;

use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\ClassNameInflector;
use EventSauce\EventSourcing\EventSourcedAggregateRootRepository;
use EventSauce\EventSourcing\MessageDecorator;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Snapshotting\AggregateRootRepositoryWithSnapshotting;
use PaymentSystem\RefundAggregateRoot;
use PaymentSystem\Repositories\RefundRepositoryInterface;

/**
 * @extends EventSourcedAggregateRootRepository<RefundAggregateRoot>
 */
class RefundRepository extends EventSourcedAggregateRootRepository implements AggregateRootRepositoryWithSnapshotting, RefundRepositoryInterface
{
    use SnapshotBehavior;

    public function __construct(
        MessageRepository $messageRepository,
        ?MessageDispatcher $dispatcher = null,
        ?MessageDecorator $decorator = null,
        ?ClassNameInflector $classNameInflector = null,
    ) {
        $this->messageRepository = $messageRepository;
        $this->className = RefundAggregateRoot::class;

        parent::__construct(RefundAggregateRoot::class, $messageRepository, $dispatcher, $decorator, $classNameInflector);
    }

    public function retrieve(AggregateRootId $aggregateRootId): RefundAggregateRoot
    {
        return parent::retrieve($aggregateRootId);
    }
}
