<?php
echo time() . "\n";
$sid = $_GET['sid'] ?? '';
$key = 'sid_' . $sid;
$redis = new Redis();
$redis->connect('03-nginx_redis_1', 6379);
$redis->ping();

//$keys = $redis->keys('sid_*');
//var_dump($keys);

while (TRUE) {
  $string = $redis->LPOP($key);
  if (!$string) {
    break;
  }
  var_dump($string) . "\n";
}
