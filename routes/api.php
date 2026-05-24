<?php

use App\Events\OrderShipped;

use App\Http\Controllers\LimiterController;
use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


/*******  !!!! 一定要注意  ！！！！
 * php81 artisan optimize 开启所有的缓存；
 * php81 artisan optimize:clear
 * -----------------------------------
 * 系统缓存：（app/bootstrap/cache）
 * 配置缓存（config .env），
 * 路由缓存，
 * 服务提供者缓存，
 * 包缓存，
 * 事件缓存；
 *
 * 业务缓存：()
 * 视图缓存，
 * Cache业务数据缓存;
 *
 *
 * | 分类 | 缓存类型 | 所在位置 (物理/逻辑) | 主要作用 | 常用命令 |
 * | :--- | :--- | :--- | :--- | :--- |
 * | 系统缓存 | 配置缓存 | `bootstrap/cache/config.php` | 将几十个配置文件合并为一个，减少文件读取IO，加速框架启动。 | 生成: `php artisan config:cache`<br>清除: `php artisan config:clear` |
 * | | 路由缓存 | `bootstrap/cache/routes-v7.php` | 将所有路由注册编译为静态数组，避免每次请求都重新解析路由文件。 | 生成: `php artisan route:cache`<br>清除: `php artisan route:clear` |
 * | | 服务/包缓存 | `bootstrap/cache/services.php` | 缓存服务提供者和包的服务清单，加速依赖注入容器的构建。 | 生成: `php artisan optimize`<br>清除: `php artisan optimize:clear` |
 * | | 事件缓存 | `bootstrap/cache/events.php` | 构建事件与监听器的映射地图，避免每次请求都扫描监听器目录。 | 生成: `php artisan event:cache`<br>清除: `php artisan event:clear` |
 * | 业务缓存 | 视图缓存 | `storage/framework/views/` | 存储 Blade 模板编译后的原生 PHP 代码，避免每次都要重新编译模板。 | 清除: `php artisan view:clear`<br>*(通常自动生成，很少手动生成)* |
 * | | 业务数据缓存 | `storage/framework/cache/` (文件)<br>或 Redis/Memcached (内存) | 存储查询结果、对象等，直接拦截数据库请求，提升响应速度。 | 清除: `php artisan cache:clear`<br>*(代码中调用 `Cache::put()` 等生成)* |
 */

use App\Http\Controllers\Strategy;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;


//没有登陆跳转到登陆页面；
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function (Request $request) {

    dd(DB::getFacadeRoot());
});


Route::get('/main', [MainController::class, 'index']);

//策略模式中的日志驱动，队列驱动，缓存驱动；
Route::get('/st', [Strategy::class, 'main']);


// 策略设计模式；

//适配器模式  日志和缓存系统； -- 适配第三方接口

//todo 日志模块的实现 第三方 monolog 日志第三方适配器的兼容

//注册模式 -- 其实就是容器模式，在服务提供者里面绑定对象，就是注册模式； done


// 事件和队列模块的实现 job event 监听通知模块；
Route::get('/queue/job', [\App\Http\Controllers\DemoJobController::class, 'index']);

//todo 自动加载 autoload  自动加载完全吃透；

//todo Symfony 框架；

//todo  new Application  + index.php 文件的整个source code  + 错误和异常处理机制；

//todo sanctum 的实现；

//todo 测试模块  phpunit 测试单元；

// todo events doing
//  一个监听者可以监听多个事件吗？？？
Route::get('/event', function () {

    echo "begin events";
    echo "\n";
    //三种使用事件的方式：
    \App\Events\OrderShipped::dispatch('aaa');

    event(new OrderShipped('bbb'));

    \Event::dispatch(new OrderShipped('ccc'));

    echo \Str::uuid();
    echo "\n";
    echo "end evetns";
    //job 就是一个job对象；
    //dispatch($job);
    \Illuminate\Support\Facades\Redis::set('ss', 11);
    \Bus::batch();
    // result : aaabbbccc
});

//todo 事物队列
Route::get('/event_queue', [\App\Http\Controllers\EventQueueController::class, 'index']);


//todo schedula 任务调度



//todo 错误和异常处理是怎么实现的？

//http cors 中间件；跨域问题； done


// limit 限流 底层类库的实现。 RateLimiter  and Limit 的区别 done;

/**
 * 一个限流器可以设置多条规则：
 * RateLimiter::for('api', function () {
 * return [
 * Limit::perMinute(60),   // 规则1
 * Limit::perHour(1000),   // 规则2
 * Limit::perDay(5000),    // 规则3
 * ];
 * });
 */

Route::get('/rate_limit', function () {
    // 创建限制器，这个一般是放在服务提供者里来创建api的限制器；
    // api仅仅是限流器的名字；
    RateLimiter::for('api', function (Request $request) {
        //配置限流规则；
        //return new Limit(
        //    $request->ip(),
        //    60,
        //    60
        //);
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });
});

//使用 限制器名字就叫api；
//这个请求使用api名字的限流器；
Route::middleware('throttle:api')->get('/user', function (Request $request) {

    echo 'limiter name : throttle:api';
});

//todo 实现一个app 容器 debug的接口
// 自定义 artisan command
Route::get('/app', function() {
    show_container_bindings();
    Cache::lock();
    dd(app());
    DB::transaction();
});

//todo 限流器的底层实现原理：滑动窗口；
Route::get('/limiter', [LimiterController::class, 'main']);
