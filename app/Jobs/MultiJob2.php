<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MultiJob2 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        //
        //setting
        $this->queue = 'multi_job2';
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
        //
        echo self::class;
        echo "\n";
        echo date('Y-m-d H:i:s') . PHP_EOL;
        sleep(10);

    }
}
