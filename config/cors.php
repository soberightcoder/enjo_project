<?php


/**
 * 其实header响应头基本都是和浏览器做交互；
 * 告诉浏览器，是否允许跨域，数据的解析规则，控制前端的可读性；
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    //允许哪些请求方式；
    //Access-Control-Allow-Methods: *
    'allowed_methods' => ['*'],

    //允许那些域名访问；
    //Access-Control-Allow-Origin: *
    'allowed_origins' => ['*'],

    //域名的正则匹配
    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],
    //允许前端读哪些header头？
    //Access-Control-Expose-Headers: X-Token
    'exposed_headers' => [],

    //只管option的预检；
    //Access-Control-Max-Age:3600;
    'max_age' => 0,

    //是否允许跨域带cookie
    //Access-Control-Allow-Credentials: false
    'supports_credentials' => false,

];
