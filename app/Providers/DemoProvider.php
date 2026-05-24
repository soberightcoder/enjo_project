<?php

namespace App\Providers;

use App\Tools\DemoTools;
use Illuminate\Support\ServiceProvider;

class DemoProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

        //是否使用反射还是在于$concrete是闭包还是类名；类名就可以使用反射来实现；
        //binds 方法是否用到反射；
        // 闭包，自己new；
        $this->app->bind('demo', function ($app) {
            return new DemoTools();
        });

        //binds 方法会用到反射
//        $this->app->bind('demo', DemoTools::class);

        //binds 这两种方法是相同的；
//        $this->app->bind(DemoTools::class);
//        $this->app->bind(DemoTools::class, DemoTools::class);

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
