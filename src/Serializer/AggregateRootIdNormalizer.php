<?php

namespace PaymentSystem\Laravel\Serializer;

use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\ClassNameInflector;
use PaymentSystem\Laravel\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AggregateRootIdNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function __construct(
        private ClassNameInflector $classInflector,
    )
    {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        assert(is_a($type, AggregateRootId::class, true));
        assert(isset($data['__type']) && isset($data['__value']));

        $type = $this->classInflector->typeToClassName($data['__type']);
        assert(is_a($type, AggregateRootId::class, true));

        return $type::fromString($data['__value']);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_a($type, AggregateRootId::class, true);
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        assert($data instanceof AggregateRootId);

        return [
            '__type' => $this->classInflector->instanceToType($data),
            '__value' => $data->toString(),
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof AggregateRootId;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            AggregateRootId::class => true,
            Uuid::class => true,
        ];
    }
}