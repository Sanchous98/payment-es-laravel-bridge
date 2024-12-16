<?php

namespace PaymentSystem\Laravel;

use EventSauce\EventSourcing\ClassNameInflector;
use EventSauce\EventSourcing\DecoratingMessageDispatcher;
use EventSauce\EventSourcing\DotSeparatedSnakeCaseInflector;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\MessageRepository\IlluminateMessageRepository\IlluminateMessageRepository;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\ServiceProvider;
use PaymentSystem\Contracts\DecryptInterface;
use PaymentSystem\Contracts\EncryptInterface;
use PaymentSystem\Laravel\Messages\CollectingMessageDecorator;
use PaymentSystem\Laravel\Messages\CollectingMessageDispatcher;
use PaymentSystem\Laravel\Messages\EventDispatcherAdapter;
use PaymentSystem\Laravel\Repository\DisputeRepository;
use PaymentSystem\Laravel\Repository\PaymentIntentRepository;
use PaymentSystem\Laravel\Repository\PaymentMethodRepository;
use PaymentSystem\Laravel\Repository\RefundRepository;
use PaymentSystem\Laravel\Repository\SnapshotRepository;
use PaymentSystem\Laravel\Repository\TokenRepository;
use PaymentSystem\Laravel\Serializer\SymfonyPayloadSerializer;
use PaymentSystem\Repositories\DisputeRepositoryInterface;
use PaymentSystem\Repositories\PaymentIntentRepositoryInterface;
use PaymentSystem\Repositories\PaymentMethodRepositoryInterface;
use PaymentSystem\Repositories\RefundRepositoryInterface;
use PaymentSystem\Repositories\TokenRepositoryInterface;
use Symfony\Component\Serializer\Serializer;

class PaymentProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            dirname(__DIR__) . '/config' => config_path(),
            dirname(__DIR__) . '/database/migrations' => database_path('migrations'),
        ]);

        $this->app->singleton(MessageRepository::class, fn(Application $app) => new IlluminateMessageRepository(
            $app[DatabaseManager::class]->connection(),
            config('payment-es.events_table'),
            serializer: new ConstructingMessageSerializer(
                payloadSerializer: new SymfonyPayloadSerializer(
                    new Serializer(iterator_to_array($app->tagged('normalizers'))),
                ),
            ),
        ));

        $this->app->singleton(EncryptInterface::class, fn(Application $app) => new Crypt($app[Encrypter::class]));
        $this->app->singleton(DecryptInterface::class, fn(Application $app) => new Crypt($app[Encrypter::class]));

        $this->app->singleton(SnapshotRepository::class, fn(Application $app) => new SnapshotRepository(
            $app[DatabaseManager::class]->connection(),
            tableName: config('payment-es.snapshots_table'),
        ));

        $this->app->singleton(MessageDispatcher::class, fn(Application $app) => new DecoratingMessageDispatcher(
            new CollectingMessageDispatcher($app->tagged('es_consumers')),
            new CollectingMessageDecorator($app->tagged('es_decorators')),
        ));

        $this->app->singleton(DisputeRepositoryInterface::class, fn(Application $app) => new DisputeRepository(
            $app[MessageRepository::class],
            $app[MessageDispatcher::class],
        ));

        $this->app->singleton(
            PaymentIntentRepositoryInterface::class,
            fn(Application $app) => new PaymentIntentRepository(
                $app[MessageRepository::class],
                $app[MessageDispatcher::class],
            )
        );

        $this->app->singleton(
            PaymentMethodRepositoryInterface::class,
            fn(Application $app) => new PaymentMethodRepository(
                $app[MessageRepository::class],
                $app[MessageDispatcher::class],
            )
        );

        $this->app->singleton(RefundRepositoryInterface::class, fn(Application $app) => new RefundRepository(
            $app[MessageRepository::class],
            $app[MessageDispatcher::class],
        ));

        $this->app->singleton(TokenRepositoryInterface::class, fn(Application $app) => new TokenRepository(
            $app[MessageRepository::class],
            $app[MessageDispatcher::class],
        ));

        $this->app->singleton(ClassNameInflector::class, fn() => new DotSeparatedSnakeCaseInflector());
        $this->app->tag(EventDispatcherAdapter::class, 'es_consumers');
        $this->app->tag(config('payment-es.serializers'), 'normalizers');
    }
}