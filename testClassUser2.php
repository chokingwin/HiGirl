<?
header("Content-type:text/html;charset=utf-8");
require_once "./user.class.php";
//require_once "./redis.class.php";
require_once "./redis2.class.php";

//$user = new user();
//$test = new test();
//$user->test();

//var_dump( $user->checkUserReg2('oQAJBwloalLWdSFQ_2Qg3zhL85Oo') );
//var_dump( $user->matchObject('oQAJBwloalLWdSFQ_2Qg3zhL85Oo','男') );
//echo "chokingwin";
/*
$redis = new RedisCluster();
$redis->connect(array('host'=>'127.0.0.1','port'=>6379));
echo $redis->get('who this');
var_dump( $redis->getRedis() );

$redis->set('name','chenwei');
echo $redis->expire('name',15);
*/

$redis = new MyRedis();
//var_dump ($redis->ttl('useroQAJBwloalLWdSFQ_2Qg3zhL85Oo') );
echo 'test';
$name = $redis->get('who this');
echo $name;
echo $redis->lrem2('testActive','chokingwin',0);
