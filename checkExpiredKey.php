
#! /usr/local/php/bin/php
<?
require_once './redis2.class.php';

$redis = new MyRedis();
$redis->setOption();

$redis->psubscribe(array('__keyevent@0__:expired'),'psCallback');

// 回调函数,这里写处理逻辑
function psCallback($redis, $pattern, $chan, $msg){
    echo "Pattern: $pattern\n";
    echo "Channel: $chan\n";
    echo "Payload: $msg\n\n";

    $ex = substr($msg,0,4);
    $OpenID = substr($msg,4);
    if( $ex=='user' ){
        if($redis->exists('chat'.$OpenID)){
            $objectOpenID = $this->redis->hget('chat' . $OpenID, 'object');
            $redis->hdel('chat' . $objectOpenID, 'object');
            $redis->hdel('chat' . $OpenID, 'object');
        }
        $this->redis->lrem('maleActive', $OpenID, 0);
        $this->redis->lrem('femaleActive', $OpenID, 0);
    }

}
?>
