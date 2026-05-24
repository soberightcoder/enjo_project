<?php

namespace App\Listeners;

use App\Events\OrderShipped;
use App\Events\QueueEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\ManuallyFailedException;

class QueueListener implements ShouldQueue
{

    //队列中的单个任务就叫做job，任务；
    //队列任务的控制权限
    use InteractsWithQueue;

    public $connection = 'redis';
    //专属，
    public $tries = 3;
    public $retryAfter = 60;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(QueueEvent | OrderShipped $event): void
    {
        //事件队列中，保存的是event事件队列；
        //
        var_dump([
            'job'          => $this->job,
            'className'    => self::class,
            'connection'   => $this->connection,  // redis
            'tries'        => $this->tries,       // 3
            'retryAfter'   => $this->retryAfter,   // 60
            'data' => $event->data,
        ]);
    }


    //这个做的很垃圾呀？？？？
    public function failed($exception = null)
    {
        if (is_string($exception)) {
            $errorMessage = $exception;
        } elseif ($exception instanceof \Throwable) {
            $errorMessage = $exception->getMessage();
        } else {
            $errorMessage = '未知错误';
        }

        \Log::error('failed_listener:' . self::class , [
            '错误信息'   => $errorMessage,
        ]);
    }
}
