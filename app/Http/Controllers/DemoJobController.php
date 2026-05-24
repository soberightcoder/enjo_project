<?php

namespace App\Http\Controllers;

use App\Jobs\DemoJob;
use Illuminate\Http\Request;

class DemoJobController extends Controller

{
    //
    public function index()
    {
        app('app');
        die;
        //
        $str = "{\"uuid\":\"5d68e3f5-b6b7-476d-b6a0-e299e25cd913\",\"displayName\":\"App\\\\Jobs\\\\DemoJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\DemoJob\",\"command\":\"O:16:\\\"App\\\\Jobs\\\\DemoJob\\\":2:{s:4:\\\"data\\\";a:2:{s:7:\\\"orderId\\\";s:13:\\\"69db62d16d243\\\";s:4:\\\"date\\\";s:19:\\\"2026-04-12 09:16:01\\\";}s:5:\\\"queue\\\";s:4:\\\"demo\\\";}\"},\"id\":\"ICWGKYDnCj8aXjqlE9r5hjOvRgfGNxTI\",\"attempts\":0}";
        $res = json_decode($str, true);
        // 2. 取出序列化的command字符串
        //command是序列化之后的对象；
        //反序列之后，可以使用对象的访问方式来访问；
        $command = $res['data']['command'];
        $jobObject = unserialize($command);
        $businessData = $jobObject->data;
        dd($businessData);

        DemoJob::dispatch([
                'orderId' => uniqid(),
                'date' => date('Y-m-d H:i:s'),
        ]);


        DemoJob::dispatch([
                'orderId' => uniqid(),
                'date' => date('Y-m-d H:i:s'),
        ]);
        dispatch(new DemoJob([
            'orderId' => uniqid(),
            'date' => date('Y-m-d H:i:s'),
        ]));

        // 这个函数是干嘛的？
//        dispatch(function() {
//           echo date('Y-m-d H:i:s');
//           sleep(10);
//        });
    }
}
