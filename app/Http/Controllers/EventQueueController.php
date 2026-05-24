<?php

namespace App\Http\Controllers;

use App\Events\QueueEvent;
use App\Events\QueueEvent1;
use Illuminate\Http\Request;

class EventQueueController extends Controller
{
    public function index()
    {
        $str = "{\"uuid\":\"13bb1d91-7e2c-4175-8f50-8cb57328af59\",\"displayName\":\"App\\\\Listeners\\\\QueueListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:27:\\\"App\\\\Listeners\\\\QueueListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:21:\\\"App\\\\Events\\\\QueueEvent\\\":1:{s:4:\\\"data\\\";a:3:{s:4:\\\"date\\\";s:10:\\\"2026-04-15\\\";s:3:\\\"uid\\\";s:13:\\\"69df0949883da\\\";s:5:\\\"event\\\";i:0;}}}s:5:\\\"tries\\\";i:3;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}}\"},\"id\":\"UGLEGRnmlpIQg4BITPf8cNL5MFuSzTaY\",\"attempts\":0}";
        $res = json_decode($str, true);
        $commandRes = unserialize($res['data']['command']);
        dd($res, $commandRes);

        //eventQueue1  queue:listener_queue
        $data = [
            'date' => date('Y-m-d'),
            'uid'  => uniqid(),
            'event' => 0
        ];
        $data1 = [
            'date' => date('Y-m-d'),
            'uid'  => uniqid(),
            'event' => 1
        ];
        QueueEvent::dispatch($data);
        QueueEvent1::dispatch($data1);

    }
}
