<?php
//require_once "jssdk.php";
//这里的AppID 和 AppSecret 来自 晨膳房
//$jssdk = new JSSDK("wx56acbf511aa96be5", "25d92ea2c176471b3b287a17c6537d56");
//$signPackage = $jssdk->GetSignPackage();

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
$objectOpenID = $redis->hget('chat' . $OpenID, 'object');
$oneUser->sendMsg($objectOpenID, 'text', '你已经断开了连接');
$redis->hdel('chat' . $objectOpenID, 'object');
$redis->hdel('chat' . $OpenID, 'object');

//两个OpenID各自加入本性排队队列,并修改队列状态
$sex1 = $redis->hget('user' . $OpenID, 'sex');
if ($sex1 == '男') {
    $redis->lpush('maleActive', $OpenID);
    $redis->hset('user' . $OpenID, 'queueStatus', 1);

} else {
    $redis->lpush('femaleActive', $OpenID);
    $redis->hset('user' . $OpenID, 'queueStatus', 2);

}

$sex2 = $redis->hget('user' . $objectOpenID, 'sex');
if ($sex2 == '男') {
    $redis->lpush('maleActive', $objectOpenID);
    $redis->hset('user' . $objectOpenID, 'queueStatus', 1);
} else {
    $redis->lpush('femaleActive', $objectOpenID);
    $redis->hset('user' . $objectOpenID, 'queueStatus', 2);
}
//}

?>

<html>
<head>
    <title>【系统提示】残忍断开中</title>
    <meta charset='utf-8'>
</head>

<body>
<input type="button" value="关闭本窗口" onclick="WeixinJSBridge.call('closeWindow');">
</body>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
    wx.config({
        debug: false,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: <?php echo $signPackage["timestamp"];?>,
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
            // 所有要调用的 API 都要加到这个列表中
            "closeWindow"
        ]
    });

    window.onload = function(){
        wx.closeWindow();
        WeixinJSBridge.call('closeWindow');
    }

</script>
</html>

