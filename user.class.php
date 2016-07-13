<?
require_once './redis2.class.php';

class user
{

    //MySQL数据库连接句柄
    private $conn = null;

    //redis数据库操作对象
    private $redis = null;

    private $topicArr = array(
        '关于自己、人生或未来的一切真相，你想知道什么？',
        '最近有什么电影看？',
        '詹姆斯刚带领骑士拿到队史上首个总冠军。你怎么看？',
        '欧洲杯你看吗?',
        '到底是男生还是女生，比较色？'
    );
    /**
     * 构造函数
     *
     *
     */
    public function __construct()
    {
        /*连接MySQL数据库*/
        $this->conn = mysql_connect('localhost', 'root', 'yuanzhenyuan2016.');
        mysql_select_db('higirl', $this->conn);

        //连接redis数据库
        $this->redis = new MyRedis();
    }

    /**
     * 发送话题
     * @param $OpenID
     * @return mixed
     */
    public function sendTopic($OpenID){
        $topicCur = $this->topicArr[array_rand( $this->topicArr , 1 )];
        $content1 = "【系统消息】已向对方发起话题，和Ta一起聊聊吧。<br><br> 话题内容：$topicCur <br><br>";
        $touser = $this->redis->hget('chat' . $OpenID, 'object');
        $ret1 = $this->sendMsg($touser,'text',$content1);
        $content2 = "【系统消息】对方向你发起话题，和Ta一起聊聊吧。<br><br> 话题内容：$topicCur <br><br>";
        $ret2 = $this->sendMsg($OpenID,'text',$content2);
        if($ret1 == 0 && $ret2 == 0){
            return 0;
        }else{
            return 'error';
        }

    }


    /**
     *  通过客服接口转发信息
     *
     */
    public function sendMsg($OpenID, $type, $content = '')
    {
        $access_token = $this->getAccessToken();
        $touser = $this->redis->hget('chat' . $OpenID, 'object');
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $access_token;
        switch ($type) {
            case 'text':
                $data = '{
                            "touser":"' . $touser . '",
                            "msgtype":"text",
                            "text":
                            {
                                    "content":"' . $content . '"
                            }
                        }';

                break;

            case 'image':
                $data = '{
                             "touser":"' . $touser . '",
                             "msgtype":"image",
                             "image":
                             {
                                     "media_id":"' . $content . '"
                             }
                         }';

                break;

            case 'voice':
                $data = '{
                              "touser":"' . $touser . '",
                              "msgtype":"voice",
                              "voice":
                              {
                                      "media_id":"' . $content . '"
                              }
                         }';

                break;

        }
        $result = $this->https_request($url, $data);
        //更新两个chat键的过期时间
        $this->redis->expire('chat' . $OpenID, 172200);
        $this->redis->expire('chat' . $touser, 172200);
        return json_decode($result)->{'errcode'};
    }


    public function getAccessToken()
    {
        $access_token = $this->redis->get('access_token');
        if ($access_token == null) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx56acbf511aa96be5&secret=25d92ea2c176471b3b287a17c6537d56";
            $res = $this->https_request($url);
            $result = json_decode($res, true);
            $access_token = $result["access_token"];
            $this->redis->set('access_token', $access_token, 7000);
            return $access_token;
        } else {
            return $access_token;
        }
    }

    /**
     *    发起匹配
     */
    /*public function matchObject2($OpenID,$sex){
        //return $OpenID.$sex;

        if($sex=='男'){
            $queue['master'] = 'maleActive';
            $queue['slavery'] = 'femaleActive';
        }else{
            $queue['master'] = 'femaleActive';
                        $queue['slavery'] = 'maleActive'
        }

        $objectOpenID = $sex=='女'? $this->redis->rpop('maleActive'):$this->redis->rpop('femaleActive');
        //$objectOpenID = $this->redis->rpop($queue['slavery']);

        //return $objectOpenID;
        if($objectOpenID == null){
            //异性队列为空，暂时无法配对
            //加入本性队列排队
            $name = $sex=='男'? 'maleActive':'femaleActive';
            //return $name;
            //$this->redis->lpush($queue['master'],$OpenID);
            $this->redis->lpush($name,$OpenID);
            return $this->getArr('1014','狼多肉少，请耐心等待，稍后再试。。');
        }else {
            //本性队列删除OpenID
            $name = $sex=='男'? 'maleActive':'femaleActive';
            //$this->redis->lpush($name,$OpenID);
            $this->redis->lrem($name,0,$OpenID);

            //两个 OpenID 加入聊天表
            $ret1 = $this->redis->hset('chat'.$OpenID,'object',$objectOpenID);
            $ret2 = $this->redis->hset('chat'.$objectOpenID,'object',$OpenID);
            if($ret1 && $ret2){
                //聊天表插入成功
                //修改两个OpenID的队列状态
                $this->redis->hset('user'.$OpenID,'queueStatus',3);
                $this->redis->hset('user'.$objectOpenID,'queueStatus',3);

                $school = $this->redis->hget('user'.$objectOpenID,'school');
                return $this->getArr('1002','匹配成功<br/>'.'学校年级专业：'.$school);
            }else {
                $this->redis->hdel('chat'.$OpenID);
                $this->redis->hdel('chat'.$objectOpenID);
                return $this->getArr('1015','匹配发生异常，请重新发起匹配。。');
            }
        }
    }*/
    public function matchObject($OpenID, $sex)
    {
        //return $OpenID.$sex;
        $objectOpenID = $sex == '女' ? $this->redis->rpop('maleActive') : $this->redis->rpop('femaleActive');
        //return $objectOpenID;
        if ($objectOpenID == null) {
            //异性队列为空，暂时无法配对
            //加入本性队列排队
            $name = $sex == '男' ? 'maleActive' : 'femaleActive';
            //return $name;
            $len = $this->redis->llen($name);
            if($len == 0){
                $this->redis->lpush($name, $OpenID);
            }else{
                for($i=0;$i<$len;$i++){
                    if($this->redis->lindex($name,$i)==$OpenID){
                        break;
                    }else{
                        $this->redis->lpush($name, $OpenID);
                        break;
                    }
                }
            }
            return $this->getArr('1014', '狼多肉少，请耐心等待，稍后再试。。');
        } else {
            //本性队列删除OpenID
            $name = $sex == '男' ? 'maleActive' : 'femaleActive';
            $this->redis->lrem($name, $OpenID, 0);

            //两个 OpenID 加入聊天表
            $ret1 = $this->redis->hset('chat' . $OpenID, 'object', $objectOpenID);
            $ret2 = $this->redis->hset('chat' . $objectOpenID, 'object', $OpenID);
            $this->redis->expire('chat' . $OpenID, 172200);
            $this->redis->expire('chat' . $objectOpenID, 172200);
            if ($ret1 && $ret2) {
                //聊天表插入成功
                //修改两个OpenID的队列状态
                $this->redis->hset('user' . $OpenID, 'queueStatus', 3);
                $this->redis->hset('user' . $objectOpenID, 'queueStatus', 3);

                $school = $this->redis->hget('user' . $objectOpenID, 'school');
                //向匹配对方发送匹配成功信息
                $school2 = $this->redis->hget('user' . $OpenID, 'school');
                $this->sendMsg($OpenID, 'text', '有人匹配到了你，ta的信息：' . $school2);
                return $this->getArr('1002', '匹配成功<br/>' . '学校年级专业：' . $school);
            } else {
                $this->redis->hdel('chat' . $OpenID);
                $this->redis->hdel('chat' . $objectOpenID);
                return $this->getArr('1015', '匹配发生异常，请重新发起匹配。。');
            }
        }
    }

    /**
     *    检查是否注册。先redis，再MySQL
     */
    public function checkUserReg2($OpenID)
    {
        $val = $this->redis->hget('user' . $OpenID, 'OpenID');
        //return $val;
        if ($val == null) {
            //return 1;
            //如果redis里不存在，从MySQL获取
            $val = $this->getUserInfo($OpenID);
            //return $val;
            if ($val['code'] == 1000) {
                //return 3;
                //MySQL里有数据，确实已经注册了
                $this->redis->multi();
                foreach ($val['data'] as $key => $value) {
                    $this->redis->hset('user' . $OpenID, $key, $value);
                }
                //设置过期时间
                $this->redis->expire('user' . $OpenID, 172200);
                $this->redis->exec();
                //return 4;
                return $val;
            } else {
                return $val;
            }
        } else {
            $arr = $this->redis->hget('user' . $OpenID);
            $this->redis->expire('user' . $OpenID, 172200);
            return $this->getArr('1000', '已注册，顺利获取到用户信息', $arr);
        }


    }

    public function getUserInfo($OpenID)
    {
        //根据openid查询MySQL中是否有注册信息
        $sql = "select OpenID from userInfo where OpenID = '$OpenID' ";
        $rs = mysql_query($sql, $this->conn);
        $row = mysql_fetch_array($rs);
        $result = $row[0];

        if ($result) {
            $sql2 = "select * from userInfo where OpenID = '$OpenID' ";
            $rs = mysql_query($sql2, $this->conn);
            $row = mysql_fetch_array($rs);
            foreach ($row as $key => $value) {
                $info[$key] = $value;
            }

            $arr = array('OpenID' => $info['OpenID'],
                'sex' => $info['sex'],
                'school' => $info['school'],
                'isDisturb' => $info['isDisturb'],
                'points' => $info['points'],
                'queueStatus' => $info['queueStatus']
            );
            return $this->getArr('1000', '已注册，顺利获取到用户信息', $arr);
        } else {
            return $this->getArr('1001', '【系统提示】<br>同学你还未注册，请先完善基本信息,才能开始匿名聊天哦！');
        }
    }

    public function checkUserReg($OpenID)
    {
        //$openid = $_GET[''];
        //根据openid查询MySQL中是否有注册信息
        $sql = "select OpenID from userInfo where OpenID = '$OpenID' ";
        $rs = mysql_query($sql, $this->conn);
        $row = mysql_fetch_array($rs);
        $result = $row[0];

        if ($result) {
            $sql2 = "select * from userInfo where OpenID = '$OpenID' ";
            $rs = mysql_query($sql2, $this->conn);
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
                '学校年级专业' => $info['学校年级专业'],
                '是否免打扰' => $info['是否免打扰'],
                '积分' => $info['积分'],
                '队列状态' => $info['队列状态']
            );
            return $this->getArr('1000', '已注册，顺利获取到用户信息', $arr);
        } else {
            /*$arr = array('ERRMsg' => '1001');
            echo json_encode($arr);*/
            return $this->getArr('1001', '系统提示：同学你还未注册，请先完善基本信息,才能开始匿名聊天哦');
        }
    }


    public function getArr($code, $message = '', $data = array())
    {
        if (!is_numeric($code))
            return "";

        $result = array(
            'code' => $code,
            'message' => $message,
            'data' => $data
        );

        return $result;
    }

    public function test()
    {
        echo "test class user" . '<br/>';
        var_dump($this->conn);
        var_dump($this->redis);
    }

    public function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}
