<?
require_once 'redis.class.php';
$redis = new RedisCluster();
$redis->connect(array('host'=>'127.0.0.1','port'=>6379));

/*连接数据库*/
$conn = mysql_connect('localhost','root','yuanzhenyuan2016.');
mysql_select_db('higirl',$conn);

/**
 *    发起匹配
 */
public function matchObejct($OpenID,$sex){
	$objectOpenID = $sex=='女'? $redis->rpop('maleActive'):$redis->rpop('femaleActive');
	if($objectOpenID == null){
		//异性队列为空，暂时无法配对
		//加入本性队列排队
		$name = $sex=='男'? 'maleActive':'femaleActive'; 
		$redis->lpush($name,$OpenID);
		return '狼多肉少，请耐心等待，稍后再试。。'
	}else {
		//本性队列删除OpenID
		$name = $sex=='男'? 'maleActive':'femaleActive'; 
		$redis->lrem($name,0,$OpenID);

		//两个 OpenID 加入聊天表
		$ret1 = $redis->hset('chat'.$OpenID,'object',$objectOpenID);
		$ret2 = $redis->hset('chat'.$objectOpenID,'object',$OpenID);
		if($ret1 && $ret2){
			//聊天表插入成功
			$school = $redis->hget('user'.$objectOpenID,'school');
			return '匹配成功<br/>'.'学校年级专业：'.$school;
		}else {
			$redis->hdel('chat'.$OpenID);
			$redis->hdel('chat'.$objectOpenID);
			return '匹配发生异常，请重新发起匹配。。';
		}
	}
}


/**
 *    检查是否注册。先redis，再MySQL
 */
public function checkUserReg2($OpenID){
	$val = $redis->hget('user'.$OpenID,'OpenID');
	if(empty($val)){
		//如果redis里不存在，从MySQL获取
		$val = getUserInfo($OpenID);
		if($val['code'] == 1000){
			//MySQL里有数据，确实已经注册了
			$redis->multi();
			foreach ($val['data'] as $key => $value) {
				$redis->hset('user'.$OpenID,$key,$value);
			}
			//设置过期时间
			$redis->expire('user'.$OpenID,172200);
			$redis->exec();
			return $val;
		}else {
			return $val;
		}
	}else {
		return $val;
	}
	

}

public function getUserInfo($OpenID){
	//根据openid查询MySQL中是否有注册信息
	$sql = "select OpenID from userInfo where OpenID = '$OpenID' ";
    $rs = mysql_query($sql,$conn);
    $row = mysql_fetch_array($rs);
	$result = $row[0];
	
	if( $result ){
		$sql2="select * from userInfo where OpenID = '$OpenID' ";
	    $rs = mysql_query($sql2,$conn);
	    $row = mysql_fetch_array($rs);
		foreach ($row as $key => $value) {
            $info[$key] = $value;
        }

		$arr = array('OpenID' => $info['OpenID'], 
					 'school' => $info['school'] , 
					 'isDisturb' => $info['isDisturb'] , 
					 'points'=>$info['points'] , 
					 'queueStatus' => $info['queueStatus']
	 	        );
		return getArr('1000','已注册，顺利获取到用户信息',$arr);
	}else {
		return getArr('1001','系统提示：同学你还未注册，请先完善基本信息,才能开始匿名聊天哦');
	}
}

public function checkUserReg($OpenID){
	//$openid = $_GET[''];
	//根据openid查询MySQL中是否有注册信息
	$sql = "select OpenID from userInfo where OpenID = '$OpenID' ";
    $rs = mysql_query($sql,$conn);
    $row = mysql_fetch_array($rs);
	$result = $row[0];
	
	if( $result ){
		$sql2="select * from userInfo where OpenID = '$OpenID' ";
	    $rs = mysql_query($sql2,$conn);
	    $row = mysql_fetch_array($rs);
		foreach ($row as $key => $value) {
            $info[$key] = $value;
        }

		/*$arr = array('ERRMsg' => '1000', 
					 'info'   => array('OpenID' => $info['OpenID'], 
					 	   			   '学校年级专业' => $info['学校年级专业'] , 
					 	               '是否免打扰' => $info['是否免打扰'] , 
					 	               '积分'=>$info['积分'] , 
					 	               '队列状态' => $info['队列状态']
					 	              )	
			   );*/
		$arr = array('OpenID' => $info['OpenID'], 
					 '学校年级专业' => $info['学校年级专业'] , 
					 '是否免打扰' => $info['是否免打扰'] , 
					 '积分'=>$info['积分'] , 
					 '队列状态' => $info['队列状态']
	 	        );
		return getArr('1000','已注册，顺利获取到用户信息',$arr);
	}else {
		/*$arr = array('ERRMsg' => '1001');
		echo json_encode($arr);*/
		return getArr('1001','系统提示：同学你还未注册，请先完善基本信息,才能开始匿名聊天哦');
	}
}


public static function getArr($code, $message='', $data=array()){
    if(!is_numeric($code))
        eturn "";

    $result = array(
        'code' => $code,
        'message' => $message,
        'data' => $data
    );

    return $result;
}
