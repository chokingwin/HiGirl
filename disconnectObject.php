<?
require_once "./redis2.class.php";
require_once "./user.class.php";

$OpenID = $_GET['OpenID'];

$redis = new MyRedis();

$oneUser = new user();
$oneUser->sendMsg($OpenID, 'text', '对方跟你断开了连接');
//$oneUser->sendMsg($objectOpenID, 'text', '对方跟你断开了连接');


//disconnectObject($OpenID);
//echo $OpenID;

//function disconnectObject($OpenID){
        //聊天表删除本匹配，共两条
        $objectOpenID = $redis->hget('chat'.$OpenID,'object');
        $oneUser->sendMsg($objectOpenID, 'text', '你已经断开了连接');
	$redis->hdel('chat'.$objectOpenID,'object');
        $redis->hdel('chat'.$OpenID,'object');

        //两个OpenID各自加入本性排队队列,并修改队列状态
        $sex1 = $redis->hget('user'.$OpenID,'sex');
        if($sex1 == '男'){
                $redis->lpush('maleActive',$OpenID);
                $redis->hset('user'.$OpenID,'queueStatus',1);

        }else{
                $redis->lpush('femaleActive',$OpenID);
                $redis->hset('user'.$OpenID,'queueStatus',2);

        }

        $sex2 = $redis->hget('user'.$objectOpenID,'sex');
        if($sex2 == '男'){
                $redis->lpush('maleActive',$objectOpenID);
                $redis->hset('user'.$objectOpenID,'queueStatus',1);
        }else{
                $redis->lpush('femaleActive',$objectOpenID);
                $redis->hset('user'.$objectOpenID,'queueStatus',2);
        }
//}

?>

<html>
<head>
<?echo '<script>window.close();</script>';?>
</head>
<body></body>
</html>

