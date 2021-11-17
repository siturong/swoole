#!/usr/bin/env php
<?php


/*$http = new Swoole\Http\Server("0.0.0.0", 9501);
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
$http->start();*/

$server = new Swoole\Websocket\Server('0.0.0.0', 9502);

$server->on('start', function ($server) {
  echo "Websocket Server is started at ws://127.0.0.1:9502\n";
});

$server->on('open', function($server, $req) {
  $redis1 = new Swoole\Coroutine\Redis();
  $redis1->connect('03-nginx_redis_1', 6379);

  $redis1->RPUSH('log', json_encode($req->fd));
  $redis1->RPUSH('log', json_encode($req->header));

  $redis1->RPUSH('client_on', $req->fd);

    echo "connection open: {$req->fd}\n";
});

$server->on('message', function($server, $frame) {
  $redis1 = new Swoole\Coroutine\Redis();
  $redis1->connect('03-nginx_redis_1', 6379);

  $arr['time'] = time();
  $arr['fd'] = $frame->fd;
  $arr['data'] = $frame->data;

  $fd = $frame->fd;
  $content = json_encode($arr);
  $exp = explode('|', $arr['data']);
  if (count($exp) == 2) {
    $fd = $exp[0];
    $content = $exp[1];
  }

  $arr['data'] = $content;
  $redis1->RPUSH('sid_' . $frame->fd,  json_encode($arr));

  $server->push($fd, $frame->fd . '|' . $content);

});

$server->on('close', function($server, $fd) {
  $redis1 = new Swoole\Coroutine\Redis();
  $redis1->connect('03-nginx_redis_1', 6379);
  //删除名称为key的list中值为value的元素
  $redis1->lRem('client_on', $fd, 0);


  echo "connection close: {$fd}\n";
});

$server->on('request', function (Swoole\Http\Request $request, Swoole\Http\Response $response) {
  global $server;//调用外部的server
  // $server->connections 遍历所有websocket连接用户的fd，给所有用户推送
  foreach ($server->connections as $fd) {
    // 需要先判断是否是正确的websocket连接，否则有可能会push失败
    if ($server->isEstablished($fd)) {
      //$server->push($fd, $request->get['message']);
    }
  }
});

$server->start();