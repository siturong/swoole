<?php
echo time() . "\n";
$sid = $_GET['sid'] ?? '';
//$key = 'sid_' . $sid;
$redis = new Redis();  //$redis = new Swoole\Coroutine\Redis();
$redis->connect('03-nginx_redis_1', 6379);
$redis->ping();

$keys = $redis->keys('sid_*');
foreach ($keys as $key) {
  $num = $redis->lLen($key);
  var_dump( 'List "' . $key . '" 个数:' . $num) . "\n";
}

/*if (!empty($keys)) {
  foreach ($keys as $key) {
    $num = $redis->lLen($key);
    var_dump( 'List ' . $key . '个数:' . $num) . "\n";
    while (TRUE) {
      $string = $redis->LPOP($key);
      if (!$string) {
        break;
      }
      var_dump($string) . "\n";
    }
  }
}*/
