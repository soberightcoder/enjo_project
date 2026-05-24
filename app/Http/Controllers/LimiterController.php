<?php

namespace App\Http\Controllers;

use Illuminate\Redis\Connections\PhpRedisConnection;
use Illuminate\Support\Facades\Redis;

class LimiterController extends Controller
{
    //
    public function main()
    {
        //基于incr + expire实现窗口的限流算法
        $this->mixedWindows();
        //基于redis zset 实现的滑动窗口
        $this->slidingwindows();

    }

    public function slidingwindows()
    {
        // ✅ 你要的 PHPDoc 完美写法，IDE 100% 识别
        /** @var PhpRedisConnection $redis */
        $redis = Redis::connection();
        //-- KEYS[1]  限流key
        //-- ARGV[1]  窗口最大请求数
        //-- ARGV[2]  窗口长度（毫秒）
        //-- ARGV[3]  当前时间戳（毫秒）
        //毫秒级别的限流
        $lua = <<<LUA
            local key = KEYS[1]
            local max = tonumber(ARGV[1])
            local window = tonumber(ARGV[2])
            local now = tonumber(ARGV[3])
            local min = now - window

            redis.call('ZREMRANGEBYSCORE', key, 0, min)
            local count = redis.call('ZCARD', key)

            if count >= max then
                return 0
            end

            redis.call('ZADD', key, now, now)
            redis.call('EXPIRE', key, window / 1000 + 10)
            return 1
        LUA;
        //当前毫秒数字
        $nowMs = (int)(microtime(true) * 1000);
        //key
        $key = 'slidingwindows:limit';
        //请求的最大请求数目；
        $max = 10;
        //多少毫秒内；
        $windowsMs = 1000;
        //执行lua
        $res = $redis->eval(
                $lua,
                1,
                $key,
                $max,
                $windowsMs,
                $nowMs
        );
        if (!$res) {
            echo "请求频繁，请稍后再试";
            exit;
        }
        echo "当前窗口计数次数:". $redis->zCard($key);
    }


    /**
     * @return void
     * 固定窗口
     * 缺点：临界突刺； laravel throttle的底层实现方式；
     */
    public function mixedWindows()
    {
        //基于内存实现的限流算法
        //incr当key不存在的时候会创建并且+1；

        $windowKey = "rate_limit:api:user_1001"; // 按用户/接口区分
        $maxRequest = 10;  // 10 次请求
        $windowTime = 1;   // 1 秒窗口
        //使用静态方法然后不识别：
        /***@var PhpRedisConnection $redis*/
        $redis = Redis::connection();
        $isAllow = $redis->eval(
            $this->getLuaScript(),
            1,               // 告诉Redis：我传了1个key
            $windowKey,   // KEYS[1]
            $maxRequest, //ARGV[1]
            $windowTime, //ARGV[2]
        );
        if (!$isAllow) {
            echo "请求频繁，请1秒后再试";
        }
        echo "当前窗口计数：". $redis->get($windowKey);

    }


    protected function getLuaScript()
    {
        return <<<LUA
        -- 获取 key、最大限制、过期时间
        local key = KEYS[1]
        local max = tonumber(ARGV[1])
        local expireSec = tonumber(ARGV[2])

        -- 计数 +1
        local count = redis.call("INCR", key)

        -- 第一次才设置过期（原子！）
        if count == 1 then
            redis.call("EXPIRE", key, expireSec)
        end

        -- 返回是否允许：1=放行 0=拒绝
        return count <= max and 1 or 0
        LUA;
    }
}
