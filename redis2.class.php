<?php
/**
 * Redis 操作，支持 Master/Slave 的负载集群
 *
 */

class MyRedis{
       
    private $redis;
       
    /**
     * 构造函数
     *
     * @param string $host 主机号
     * @param int    $port 端口号
     */
    public function __construct($host='127.0.0.1',$port=6379){
        $this->redis = new redis();
        $this->redis->connect($host,$port);
    }
       
    /**
     * 写缓存
     *
     * @param string $key 组存KEY
     * @param string $value 缓存值
     * @param int $expire 过期时间， 0:表示无过期时间
     */
    public function set($key, $value, $expire=0){
        // 永不超时
        if($expire == 0){
            $ret = $this->redis->set($key, $value);
        }else{
            $ret = $this->redis->setex($key, $expire, $value);
        }
        return $ret;
    }
       
    /**
     * 读缓存
     *
     * @param string $key 缓存KEY,支持一次取多个 $key = array('key1','key2')
     * @return string || boolean  失败返回 false, 成功返回字符串
     */
    public function get($key){
        // 是否一次取多个值
        $func = is_array($key) ? 'mGet' : 'get';
        return $this->redis->{$func}($key);
    }

    /**
     * 条件形式设置缓存，如果 key 不存时就设置，存在时设置失败
     *
     * @param string $key 缓存KEY
     * @param string $value 缓存值
     * @return boolean
     */
    public function setnx($key, $value){
        return $this->redis->setnx($key, $value);
    }
       
    /**
     * 删除缓存
     *
     * @param string || array $key 缓存KEY，支持单个健:"key1" 或多个健:array('key1','key2')
     * @return int 删除的健的数量
     */
    public function remove($key){
        // $key => "key1" || array('key1','key2')
        return $this->redis->delete($key);
    }
       
    /**
     * 值加加操作,类似 ++$i ,如果 key 不存在时自动设置为 0 后进行加加操作
     *
     * @param string $key 缓存KEY
     * @param int $default 操作时的默认值
     * @return int　操作后的值
     */
    public function incr($key,$default=1){
        if($default == 1){
            return $this->redis->incr($key);
        }else{
            return $this->redis->incrBy($key, $default);
        }
    }
       
    /**
     * 值减减操作,类似 --$i ,如果 key 不存在时自动设置为 0 后进行减减操作
     *
     * @param string $key 缓存KEY
     * @param int $default 操作时的默认值
     * @return int　操作后的值
     */
    public function decr($key,$default=1){
        if($default == 1){
            return $this->redis->decr($key);
        }else{
            return $this->redis->decrBy($key, $default);
        }
    }
       
    /**
     * 添空当前数据库
     *
     * @return boolean
     */
    public function clear(){
        return $this->redis->flushDB();
    }
       
    /**
     *    lpush 
     */
    public function lpush($key,$value){
        return $this->redis->lpush($key,$value);
    }

    /**
     *    rpop 
     */
    public function rpop($key,$value){
        return $this->redis->rpop($key);
    }

    /**
     *    add lpop
     */
    public function lpop($key){
        return $this->redis->lpop($key);
    }
    /**
     * lrange 
     */
    public function lrange($key,$start,$end){
        return $this->redis->lrange($key,$start,$end);    
    }
    /**
     *    lrem
     */
    public function lrem($key,$value,$count){
    	return $this->redis->lrem($key,$value,$count);
    }
    /**
     *    set hash opeation
     */
    public function hset($name,$key,$value){
        if(is_array($value)){
            return $this->redis->hset($name,$key,serialize($value));    
        }
        return $this->redis->hset($name,$key,$value);
    }
    /**
     *    get hash opeation
     */
    public function hget($name,$key = null,$serialize=true){
        if($key){
            $row = $this->redis->hget($name,$key);
            if($row && $serialize){
                unserialize($row);
            }
            return $row;
        }
        return $this->redis->hgetAll($name);
    }

    /**
     *    delete hash opeation
     */
    public function hdel($name,$key = null){
        if($key){
            return $this->redis->hdel($name,$key);
        }
        return $this->redis->hdel($name);
    }
    /**
     * Transaction start
     */
    public function multi(){
        return $this->redis->multi();    
    }
    /**
     * Transaction send
     */

    public function exec(){
        return $this->redis->exec();    
    }


    /******************接下来这一部分是我自己写的**********************/
    public function expire($key=null,$time=0){
       return $this->redis->expire($key,$time);
    }

    public function psubscribe($patterns=array(),$callback){
        $this->redis->psubscribe($patterns,$callback);
    }

    /*function psCallback($redis, $pattern, $chan, $msg){
        echo "Pattern: $pattern\n";
        echo "Channel: $chan\n";
        echo "Payload: $msg\n";
    }*/

    public function setOption(){
        $this->redis->setOption(Redis::OPT_READ_TIMEOUT,-1);
    }

    public function exists($key){
        return $this->redis->exists($key);
    }

    public function llen($key){
        return $this->redis->lLen($key);
    }

    public function lindex($key,$index){
        return $this->redis->lIndex($key,$index);
    }
       
}// End Class
       
