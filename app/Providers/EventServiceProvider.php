<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Listeners\SendShipmentNotification;
use App\Events\OrderShipped;
use App\Listeners\QueueListener;
use App\Events\QueueEvent;
use App\Listeners\QueueListener1;
use App\Events\QueueEvent1;
use App\Listeners\Listener1;
use App\Events\Event1;
use App\Listeners\Listener2;
use App\Events\Event2;
use App\Listeners\Listener3;
use App\Events\Event3;
use Illuminate\Queue\Events\JobFailed;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        OrderShipped::class => [
            QueueListener::class,
            QueueListener1::class,
        ],
        QueueEvent::class => [
          QueueListener::class,
        ],
        QueueEvent1::class => [
            QueueListener1::class,
        ],
        Event1::class => [
            Listener1::class
        ],
        Event2::class =>  [
            Listener2::class
        ],
        Event3::class => [
            Listener3::class,
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // 手动 注册监听器
//        Event::listen([
//            PodcastProcessed::class,
//            [SendPodcastNotification::class, 'handle']
//        ]);
//        Event::listen(function() {
//           //闭包监听器
//        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
