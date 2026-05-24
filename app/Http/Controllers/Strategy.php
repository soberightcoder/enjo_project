<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class Strategy extends Controller
{

    public $aliases = [];

    function main(Request $request)
    {
        dd(app());
        app()->bind('strage', Strategy::class);
//        dd(app());
        // 第一次 $abstract !== $concrete  make($concrete);
        // 第二次 $abstract === $concrete; build();
        dd(app("strage"));
        app()->bind('a', 'b');
        app()->alias('a', 'c');
        app('c'); // b 不存在
        dd(app());
        $this->aliases = [
            'a' => 'b',
            'b' => 'c',
        ];
        $res = $this->getAlias('a');
        dd($res);

        //resolved 解析数组
        //instances 单例数组
        //binds 绑定的实力化；
        //用反射，完全可以实例化；
        $main = resolve(MainController::class);
        $main->setId(1);
        $main->index();
        //其实一开始就会注册到这个门面设计模式的对象；
        //不然直接报错；


//        Cache::set('ceshi', 123);

//        Cache::driver('redis')->set('wen', 'shuai');
        $res = Cache::driver('redis')->get('wen');
        dd($res);
        //门面设计模式；
        \Demo::index();

        $res = Cache::get('ceshi');
        dd($res);
    }

    public function getAlias($abstract)
    {
        return isset($this->aliases[$abstract])
            ? $this->getAlias($this->aliases[$abstract])
            : $abstract;
    }
}

