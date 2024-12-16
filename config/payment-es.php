<?php

use PaymentSystem\Laravel\Serializer\AggregateRootIdNormalizer;
use PaymentSystem\Laravel\Serializer\CreditCardNormalizer;
use PaymentSystem\Laravel\Serializer\MoneyNormalizer;
use PaymentSystem\Laravel\Serializer\SourceNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use PaymentSystem\Laravel\Uuid;

return [
    'events_table' => 'stored_events',
    'snapshots_table' => 'snapshots',
    'queue' => 'sync',
    'normalizers' => [
        MoneyNormalizer::class,
        SourceNormalizer::class,
        CreditCardNormalizer::class,
        AggregateRootIdNormalizer::class,
        ObjectNormalizer::class,
    ],
];