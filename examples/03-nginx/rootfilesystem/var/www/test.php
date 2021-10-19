<?php

$redis = new Redis();
//$redis = new Swoole\Coroutine\Redis();
$redis->connect('03-nginx_redis_1', 6379);
$redis->ping();
//$redis->LPUSH('click', rand(1000, 5000));

while (true) {
  $string = $redis->LPOP('click');
  if (!$string) {
    break;
  }

  var_dump($string) . "\n";
}
