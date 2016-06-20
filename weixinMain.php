<?php

require_once "./wechatCallbackapi.class.php";

$wechatObj = new wechatCallbackapi();

if (!isset($_GET['echostr'])) {
    $wechatObj->responseMsg();
}else{
    $wechatObj->valid();
}



?>
