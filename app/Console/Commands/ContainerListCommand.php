<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ContainerListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'container:list {--key=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '显示容器的的关键状态';


    protected static array $properties = [
            'bindings'        => '服务绑定定义',
            'instances'       => '已注册实例',
            'abstractAliases' => '抽象别名映射',
            'resolved'        => '已解析服务标记'
        ];
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. 安全检查：仅限本地环境
        if (!app()->isLocal()) {
            $this->error('此命令仅限 local 环境运行');
            return 1;
        }

        // 2. 获取容器状态（核心逻辑）
        $containerData = $this->getContainerState();

        $key = $this->option('key') ?? false;

        // false 返回所有的数据
        if (!$key) {
            dd($containerData);
        }
        //true && 有意义的参数；
        isset(self::$properties[$key]) ?  dd($containerData[$key]) : $this->error('无意义的参数 key');
    }


    protected function getContainerState(): array
    {
        $container = app();
        $reflection = new \ReflectionClass($container);
        $result = [];

        foreach (self::$properties as $prop => $desc) {
            try {
                if (!$reflection->hasProperty($prop)) {
                    $result[$prop] = [
                        'status' => 'missing',
                        'message' => '属性不存在 (Laravel 版本差异)',
                        'description' => $desc
                    ];
                    continue;
                }

                $property = $reflection->getProperty($prop);
                $property->setAccessible(true);
                $value = $property->getValue($container);

                $result[$prop] = [
                    'status' => 'success',
                    'description' => $desc,
                    'count' => is_array($value) ? count($value) : null,
                    'data' => $value
                ];
            } catch (\Throwable $e) {
                $result[$prop] = [
                    'status' => 'error',
                    'description' => $desc,
                    'message' => $e->getMessage(),
                    'line'  => $e->getLine()
                ];
            }
        }

        return $result;
    }
}
