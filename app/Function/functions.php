<?php


// !!!不生效，需要运行 composer dump-autoload 加载文件；
//debug app
if (!function_exists('show_container_bindings')) {
    function show_container_bindings()
    {

        $container = app();
        $debugData = [];

        // 定义容器要查看的属性列表
        $propertiesToInspect = [
            'bindings' => '服务绑定定义 (Bindings)',
            'instances' => '已存在的实例 (Instances)',
            'abstractAliases' => '抽象别名 (Aliases)',
            'resolved' => '已解析标记 (Resolved)',
        ];

        try {
            $reflection = new ReflectionClass($container);
        } catch (Exception $e) {
            $debugData['app'] = '⚠️ 反射失败:' . $e->getMessage();
            dd($debugData);
        }

        foreach ($propertiesToInspect as $propName => $label) {
            try {

                // 1. 检查属性是否存在 (兼容性检查)
                if (!$reflection->hasProperty($propName)) {
                    $debugData[$propName] = "❌ 属性不存在 (当前 Laravel 版本可能已移除或更名)";
                    continue;
                }

                // 2. 获取属性并强制访问
                $property = $reflection->getProperty($propName);
                $property->setAccessible(true);
                $value = $property->getValue($container);

                // 3. 简单的统计信息
                $count = is_array($value) ? count($value) : 'N/A';
                $debugData[$propName] = [
                    'label' => $label,
                    'count' => $count,
                    'data' => $value
                ];


            } catch (Exception $e) {
                $debugData[$propName] = "⚠️ 获取失败: " . $e->getMessage();
            }

        }


        // 2. 最后追加【延迟绑定服务】（全量未注册的服务）
        try {
            $cacheFile = base_path('bootstrap/cache/services.php');
            $deferredData = ['label' => '延迟绑定服务 (Deferred)', 'count' => 0, 'data' => []];

            if (file_exists($cacheFile)) {
                $services = require $cacheFile;
                $deferred = $services['deferred'] ?? [];
                $deferredData['count'] = count($deferred);
                $deferredData['data'] = $deferred;
            } else {
                $deferredData['data'] = '❌ 请执行 php artisan optimize 生成服务缓存';
            }

            $debugData['deferred_services'] = $deferredData;
        } catch (Exception $e) {
            $debugData['deferred_services'] = "⚠️ 延迟服务获取失败：{$e->getMessage()}";
        }

        // 输出结果
        dd($debugData);
    }
}









