<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);
//捕获 $request http请求
//request -> 全局中间件前置修饰 -> 根据 request中的uri + 请求方式 在路由中匹配控制器 -> 路由中间件 -> 控制器 -> response；
//控制器 -> response -> 全局中间件后置修饰 ->  路由中间件后置修饰-> 响应；
// 从路由到控制器 + 方法，这里肯定是用的反射的方法来做实例化控制器类；
// 反射 + 容器；app->make(class_name); 不存在类名，容器会自动做反射的处理；然后再用反射处理方法的参数，如果存在类，就实现注入就好了！！！
$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
