<?php

namespace App\Listeners;

use App\Events\OrderShipped;
use App\Events\QueueEvent1;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class QueueListener1 implements ShouldQueue
{
    /**
     * Create the event listener.
     */

    //队列配置
    use InteractsWithQueue;

    public $connection = 'redis';
    //专属，
    public $tries = 3;
    public $retryAfter = 60;


    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(QueueEvent1 | OrderShipped $event): void
    {
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
