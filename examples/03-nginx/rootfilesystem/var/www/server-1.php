#!/usr/bin/env php
<?php

//declare(strict_types=1);

$http = new Swoole\Http\Server("0.0.0.0", 9501);
$http->on(
  "request",
  function (Swoole\Http\Request $request, Swoole\Http\Response $response) use(&$result) {
    $redis1 = new Swoole\Coroutine\Redis();
    $redis1->connect('03-nginx_redis_1', 6379);
    //$redis1->setDefer(); //先发送请求，不等待结果
    //$redis1->set('hello', 'world');
    $t =  time();
    $redis1->LPUSH('click', $t);
    //$string = $redis1->get('hello');
    //$string = $redis1->LPOP('click');


    $output = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <form method="post" action="">
     <input type="radio" value="石头" name="result">石头
     <input type="radio" value="剪刀" name="result">剪刀
     <input type="radio" value="布" name="result">布
     <button type="submit">提交</button>
     </form>' . $t . "\n";

    $arr = explode('/', trim($request->server['request_uri'], '/'));
    $output .= json_encode($arr);

    if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {

      $response->end();
      return;
    }

    if ($request->server['request_method'] == 'GET') {
      $response->end($output);
    }
    elseif ($request->server['request_method'] == 'POST') {
      $response->end("POST");
      return;
    }


  }
);
$http->start();
