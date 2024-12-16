<?php

namespace PaymentSystem\Laravel;

use EventSauce\EventSourcing\ClassNameInflector;
use EventSauce\EventSourcing\DotSeparatedSnakeCaseInflector;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\MessageRepository\IlluminateMessageRepository\IlluminateMessageRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\ServiceProvider;
use PaymentSystem\Contracts\DecryptInterface;
use PaymentSystem\Contracts\EncryptInterface;
use PaymentSystem\Laravel\Messages\EventDispatcherAdapter;
use PaymentSystem\Laravel\Messages\QueueMessageDispatcher;
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
    public function register(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/config/payment-es.php', 'payment-es');

        $this->app->bind(
            MessageRepository::class,
            fn(Application $app, array $parameters) => new IlluminateMessageRepository(
                $app[DatabaseManager::class]->connection(),
                $parameters['tableName'],
                serializer: new ConstructingMessageSerializer(
                    payloadSerializer: new SymfonyPayloadSerializer(
                        new Serializer(iterator_to_array($app->tagged('normalizers'))),
                    ),
                ),
            )
        );

        $this->app->singleton(EncryptInterface::class, Crypt::class);
        $this->app->singleton(DecryptInterface::class, Crypt::class);

        $this->app->singleton(
            SnapshotRepository::class,
            fn(Application $app, array $parameters) => new SnapshotRepository(
                $app[DatabaseManager::class]->connection(),
                tableName: $parameters['tableName'],
            )
        );

        $this->app->when(PaymentMethodRepository::class)
            ->needs(MessageRepository::class)
            ->give(
                fn(Application $app) => $app
                    ->make(MessageRepository::class, ['tableName' => config('payment-es.events_table')])
            );

        $this->app->when(RefundRepository::class)
            ->needs(MessageRepository::class)
            ->give(
                fn(Application $app) => $app
                    ->make(MessageRepository::class, ['tableName' => config('payment-es.events_table')])
            );

        $this->app->when(DisputeRepository::class)
            ->needs(MessageRepository::class)
            ->give(
                fn(Application $app) => $app
                    ->make(MessageRepository::class, ['tableName' => config('payment-es.events_table')])
            );

        $this->app->when(PaymentIntentRepository::class)
            ->needs(MessageRepository::class)
            ->give(
                fn(Application $app) => $app
                    ->make(MessageRepository::class, ['tableName' => config('payment-es.events_table')])
            );

        $this->app->when(TokenRepository::class)
            ->needs(MessageRepository::class)
            ->give(
                fn(Application $app) => $app
                    ->make(MessageRepository::class, ['tableName' => config('payment-es.events_table')])
            );

        $this->app->singleton(DisputeRepositoryInterface::class, DisputeRepository::class);
        $this->app->singleton(PaymentIntentRepositoryInterface::class, PaymentIntentRepository::class);
        $this->app->singleton(PaymentMethodRepositoryInterface::class, PaymentMethodRepository::class);
        $this->app->singleton(RefundRepositoryInterface::class, RefundRepository::class);
        $this->app->singleton(TokenRepositoryInterface::class, TokenRepository::class);
        $this->app->singleton(ClassNameInflector::class, DotSeparatedSnakeCaseInflector::class);
        $this->app->singleton(MessageDispatcher::class, QueueMessageDispatcher::class);

        $this->app->when(QueueMessageDispatcher::class)
            ->needs('$dispatchers')
            ->giveTagged('es_dispatchers');

        $this->app->tag([EventDispatcherAdapter::class], 'es_dispatchers');
        $this->app->tag(config('payment-es.normalizers'), 'normalizers');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                dirname(__DIR__) . '/config' => config_path(),
                dirname(__DIR__) . '/database/migrations' => database_path('migrations'),
            ]);
        }
    }
}