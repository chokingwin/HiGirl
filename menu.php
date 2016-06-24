<?php

//写在前面：1.注意type为view类型的子菜单，url值不能为空，且必须以"http://" 开头。
//          2.主菜单的结构为： "button": []   []中填写菜单项，以{}为一项菜单项。  单个菜单项的参数有3个或4个。
//          3.子菜单的结构为： "sub_button": []中填写子菜单项，以{}为一项子菜单项  单个菜单项的参数有3个或4个。


//此access_token使用时间仅为7200s,下次使用需要重新获取
$access_token = "oNPeGPaqyRYj0KRIRH14WvxS3IGFIqII2yWkxbsJlAnM98S7Y-mJ-4F25NI6ks-ihqSzo6x0ilTrKgdDXAuJuDKggIVw9Oufq35oda2dKr72GyiknC_pfM2fE7FSKgNOXTPhAFAEAW";

$jsonmenu = '
{
    "button": [
        {
            "name": "点我开始", 
            "sub_button": [
                {
                    "type": "click", 
                    "name": "发起聊天",
                    "key": "start"
                }, 
                {
                    "type": "click", 
                    "name": "断开聊天", 
                    "key": "end"
                }
            ]
        },
        {
            "name":"锦囊",
            "sub_button":[
                {
                    "type": "click",
                    "name": "发起话题",
                    "key": "topic"
                }
            ]
        }
    ]
}
';


$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
$result = https_request($url, $jsonmenu);
var_dump($result);

function https_request($url,$data = null){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

?>
