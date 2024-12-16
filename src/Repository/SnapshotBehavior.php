<?php

namespace PaymentSystem\Laravel\Repository;

use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Snapshotting\AggregateRootWithSnapshotting;

trait SnapshotBehavior
{
    /** @var class-string<AggregateRootWithSnapshotting> */
    private string $className;

    private MessageRepository $messageRepository;

    private SnapshotRepository $snapshotRepository;

    public function retrieveFromSnapshot(AggregateRootId $aggregateRootId): object
    {
        $snapshot = $this->snapshotRepository->retrieve($aggregateRootId);

        return $this->className::reconstituteFromSnapshotAndEvents($snapshot, $this->messageRepository->retrieveAllAfterVersion($aggregateRootId, $snapshot->aggregateRootVersion()));
    }

    public function storeSnapshot(AggregateRootWithSnapshotting $aggregateRoot): void
    {
        $this->snapshotRepository->persist($aggregateRoot->createSnapshot());
    }
}
