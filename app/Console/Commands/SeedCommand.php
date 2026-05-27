<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Process\Pool;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SeedCommand extends Command
{
    protected $signature = 'app:seed {--processes=0}';
    protected $description = '批量生成用户数据（支持多进程）';

    public function handle()
    {
        $processes = (int)$this->option('processes');

        // 模式 A：主进程模式 (负责生成子进程)
        if ($processes > 0) {
            $this->info("主进程启动，正在派发 {$processes} 个子进程...");
            $this->spawn($processes);
            $this->info("所有子进程任务完成。");
            return; // 主进程干完活就退出，不要往下跑了
        }

        // 模式 B：工作进程模式 (负责真正干活)
        $this->info("工作进程 [" . getmypid() . "] 开始插入数据...");
        $this->insertData(10000);
    }

    /**
     * 启动子进程池
     */
    public function spawn($processes)
    {
        Process::pool(function (Pool $pool) use ($processes) {
            for ($i = 0; $i < $processes; $i++) {
                // 注意：这里不传 --processes 参数，让子进程默认进入工作模式
                $pool->command('php artisan app:seed')->timeout(60 * 10);
            }
            //启动子进程
        })->start(function ($type, $output) {
            //启动多进程，并实时把子进程的输出打印到屏幕 其实就是 . 表明还在运行
            echo $output;
            ///等着所有子进程跑完，主进程再继续
        })->wait();
    }

    /**
     * 执行批量插入
     */
    public function insertData($count)
    {
        $batchSize = 500; // 每 500 条插入一次
        $data = [];

        for ($i = 1; $i <= $count; $i++) {
            $data[] = [
                'name' => fake()->name(),
                //'email' => fake()->unique()->safeEmail(),
                // 多进程对于 unique没有什么用！！！（单机内存用集合来去重呗）
                //'email' => fake()->unique()->companyEmail(),
                'email' => $this->uniqueEmailForMultiProcess(),
                'email_verified_at' => now()->subHours(rand(0,9999))->subMinutes(rand(0,9999)),
                'created_at' => now()->subHours(rand(0,9999))->subMinutes(rand(0,9999)),
                'password' => 'password',
                // 使用随机字符串避免多进程 unique 冲突
                'remember_token' => Str::random(10),
            ];

            // 凑够一批就插入
            if (count($data) >= $batchSize) {
                $this->insertBatch($data);
                $data = []; // 清空数组
                echo "."; // 进度条
            }
        }

        // 插入剩余不足 500 条的数据
        if (!empty($data)) {
            $this->insertBatch($data);
        }

        $this->line("\n工作进程完成。");
    }

    /**
     * 执行单次批量插入（带异常处理）
     */
    protected function insertBatch(array $data)
    {
        try {
            // 使用 DB::table 比 Model::create 快很多
            DB::table('users')->insert($data);
        } catch (\Exception $e) {
            //email，很容易生成重复的，
            //$this->error("插入失败: " . $e->getMessage());
            // 如果是唯一键冲突，可以忽略；其他错误可以记录日志
        }
    }

    /**
     * @return string
     * 获取多进程唯一性，邮箱email
     */
    protected function uniqueEmailForMultiProcess()
    {
        return getmypid() . '_' . fake()->companyEmail();
    }
}
