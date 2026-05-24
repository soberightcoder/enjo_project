<?php

namespace App\Providers;

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\Events\JobFailed;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
       //  ✅ 是 所有提供者先跑 register，跑完了再一起跑 boot！

        //普通绑定
        $this->app->bind(MainController::class, function ($app) {
            return new MainController();
        });
        // binds 绑定实例
//        $this->app->singleton(MainController::class, function ($app) {
//           return new MainController();
//        });

        //单例实例
//        $instance = new MainController;
//        $this->app->instance(MainController::class, $instance);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //监听素有的失败队列；
        Queue::failing(function (JobFailed $event) {
            // $event->connectionName
            // $event->job
            // $event->exception
            // ======================================
            // 1. 获取核心信息（100%安全可用）
            // ======================================
            $connection = $event->connectionName; // 连接：redis
            $queueName  = $event->job->getQueue(); // 队列名：default/high
            $payload    = $event->job->payload(); // 原始任务数据（序列化完整信息）
            $errorMsg   = $event->exception->getMessage(); // 错误信息
            $trace      = $event->exception->getTraceAsString(); // 堆栈

            // ======================================
            // 2. 你的处理逻辑（记录日志/死信/告警）
            // ======================================
            Log::error(' failed_event:', [
                '队列' => $queueName,
                '任务' => $payload,
                '错误' => $errorMsg,
                '连接' => $connection,
                '$trace' => $trace
            ]);

            // 可选：推送到你的死信队列
            // dispatch(new DeadLetterJob($payload, $errorMsg))->onQueue('dead_letter');
        });
    }
}
