<?php

namespace PaymentSystem\Laravel\Repository;

use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\ClassNameInflector;
use EventSauce\EventSourcing\EventSourcedAggregateRootRepository;
use EventSauce\EventSourcing\MessageDecorator;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Snapshotting\AggregateRootRepositoryWithSnapshotting;
use PaymentSystem\Repositories\TokenRepositoryInterface;
use PaymentSystem\TokenAggregateRoot;

/**
 * @extends EventSourcedAggregateRootRepository<TokenAggregateRoot>
 */
class TokenRepository extends EventSourcedAggregateRootRepository implements AggregateRootRepositoryWithSnapshotting, TokenRepositoryInterface
{
    use SnapshotBehavior;

    public function __construct(
        MessageRepository $messageRepository,
        ?MessageDispatcher $dispatcher = null,
        ?MessageDecorator $decorator = null,
        ?ClassNameInflector $classNameInflector = null,
    ) {
        $this->messageRepository = $messageRepository;
        $this->className = TokenAggregateRoot::class;

        parent::__construct(TokenAggregateRoot::class, $messageRepository, $dispatcher, $decorator, $classNameInflector);
    }

    public function retrieve(AggregateRootId $aggregateRootId): TokenAggregateRoot
    {
        return parent::retrieve($aggregateRootId);
    }
}