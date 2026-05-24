<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Event;

class DemoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        //setting
        $this->queue = 'demo';
        //任务最大重试次数（超过次数 → 进入失败队列）
        $this->tries = 3;
        //重试间隔，
        $this->retryAfter = 5;

        //data
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //这里是处理队列业务逻辑的代码；
        //数据本身就是job，所以直接使用$this->data就可以了；
        //队列中的任务job就是序列化后的job对象；
        echo self::class;
        echo "\n";
        echo date('Y-m-d H:i:s') . PHP_EOL;
        sleep(10);
    }

    public function failed(\Throwable $exception)
    {

        // 你想做的失败处理：
        // 1. 记录日志
        // 2. 发送告警通知
        // 3. 推入死信队列（你的需求）

        // 把失败任务丢进死信队列
        //dispatch(
        //    new DeadLetterJob([
        //        'listener:' => self::class,       // 哪个监听器失败
        //        'raw_payload' => serialize(new self::class),
        //        'error' => $exception->getMessage(), // 异常信息
        //        'trace' => $exception->getTraceAsString() // 堆栈（可选）]
        //    ])
        //);
    }
}
