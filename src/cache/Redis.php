<?php
namespace cockroach\cache;

use cockroach\exceptions\Exception;
use cockroach\extensions\EString;
use cockroach\extensions\EUpstream;

/**
 * Class Redis
 * @example
 * @package cockroach\cache
 * @datetime 2019/9/16 11:30
 * @author roach
 * @email jhq0113@163.com
 */
class Redis extends Cache
{
    /**
     * @var array
     * @datetime 2019/9/16 17:11
     * @author roach
     * @email jhq0113@163.com
     */
    public $servers = [];

    /**
     * @var \Redis
     * @datetime 2019/9/16 17:12
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_client;

    /**
     * @var \Redis
     * @datetime 2019/9/17 9:48
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_multi;

    /**
     * Lua释放锁脚本
     */
    const UNLOCK_SCRIPT = <<<LUA
            if redis.call('get', KEYS[1]) == ARGV[1]
            then
                return redis.call('del', KEYS[1])
            end
            return 0
LUA;


    /**
     * @return \Redis|null
     * @datetime 2019/9/16 17:20
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _client()
    {
        if($this->_client instanceof \Redis) {
            return $this->_client;
        }

        $server = EUpstream::consistentHash($this->servers);
        $class = isset($server['class']) ? $server['class'] : \Redis::class;
        /**
         * @var \Redis $redis
         */
        $redis = new $class();
        $timeout = isset($server['timeout']) ? $server['timeout'] : 3;
        $isConnect = $redis->connect($server['host'],$server['port'],$timeout);
        if(!$isConnect) {
            return null;
        }

        //验证密码
        if(isset($server['auth']) && !empty($server['auth'])) {
            $isAuth = $redis->auth($server['auth']);
            if(!$isAuth) {
                return null;
            }
        }

        //选库
        $isSelect = $redis->select($server['db']);
        if(!$isSelect) {
            return null;
        }

        $this->_client = $redis;
        return $this->_client;
    }

    /***管道与事务支持，默认为管道
     * @param int $mode
     * @return \Redis|void
     * @datetime 2019/9/17 10:18
     * @author roach
     * @email jhq0113@163.com
     */
    public function multi($mode=\Redis::PIPELINE)
    {
        $this->_multi = $this->_client()->multi($mode);
        return $this->_multi;
    }

    /**提交管道或事务
     * @return array
     * @datetime 2019/9/17 10:19
     * @author roach
     * @email jhq0113@163.com
     */
    public function exec()
    {
        $result = $this->_multi->exec();
        $this->_multi = null;
        return $result;
    }

    /**动态调用，支持redis原生指令
     * @param string $method
     * @param array  $params
     * @return mixed
     * @datetime 2019/9/17 9:52
     * @author roach
     * @email jhq0113@163.com
     */
    public function call($method, $params)
    {
        $redis = is_null($this->_multi) ? $this->_client() : $this->_multi;
        return call_user_func_array([$redis, $method],$params);
    }

    /**加锁
     * @param string      $key
     * @param int         $timeout
     * @param \Redis|null $redis
     * @return bool|string
     * @datetime 2019/9/17 10:37
     * @author roach
     * @email jhq0113@163.com
     */
    public function lock($key,$timeout = 8,\Redis $redis = null)
    {
        $redis = is_null($redis) ? $this->_client() : $redis;

        //创建token
        $token = uniqid().EString::createRandStr(5);

        $isLock = $redis->set($key,$token,['NX','EX' => $timeout]);
        if(!$isLock) {
            return false;
        }

        return $token;
    }

    /**释放锁
     * @param string $key
     * @param string $token
     * @param \Redis|null $redis
     * @return mixed|void
     * @datetime 2019/9/17 10:45
     * @author roach
     * @email jhq0113@163.com
     */
    public function unlock($key,$token,\Redis $redis = null)
    {
        $redis = is_null($redis) ? $this->_client() : $redis;

        $hash = $redis->script('load',self::UNLOCK_SCRIPT);
        return $redis->evalSha($hash,[ $key, $token ],1);
    }

    /**
     * @param string $key
     * @return string
     * @datetime 2019/9/16 16:47
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _getValue($key)
    {
        if(!is_null($this->_multi)) {
            return $this->_multi->get($key);
        }

        $value = $this->_client()->get($key);
        if($value === false) {
            return null;
        }

        return $value;
    }

    /**
     * @param string $key1
     * @return array
     * @datetime 2019/9/16 16:48
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _mget($key1)
    {
        $args = func_get_args();

        if(!is_null($this->_multi)) {
            return $this->_multi->mget($args);
        }

        $values = $this->_client()->mget($args);
        return array_combine($args,$values);
    }

    /**
     * @param string $key
     * @return bool
     * @datetime 2019/9/16 16:48
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _exists($key)
    {
        return $this->call('exists',[ $key ]);
    }

    /**
     * @param string $key1
     * @return bool
     * @datetime 2019/9/16 16:50
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _delete($key1)
    {
        return $this->call('delete', func_get_args());
    }

    /**
     * @param string $key
     * @param string $value
     * @param int    $timeout
     * @return bool|mixed
     * @datetime 2019/9/16 16:51
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _saveValue($key, $value, $timeout)
    {
        if($timeout == 0) {
            return $this->call('set',[ $key, $value]);
        }
        return $this->call('set',[ $key, $value, $timeout]);
    }

    /**
     * @return bool
     * @throws Exception
     * @datetime 2019/9/16 16:54
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _flush()
    {
        throw new Exception("I'm sorry! have not supported");
    }

    /**使用\Redis的PIPELINE提交
     * @return bool
     * @datetime 2019/9/16 13:31
     * @author roach
     * @email jhq0113@163.com
     */
    public function commit()
    {
        if($this->_itemStack->isEmpty()) {
            return true;
        }

        //开启管道
        $this->multi();

        while(!$this->_itemStack->isEmpty()){
            $this->save($this->_itemStack->pop());
        }

        //提交管道
        $result = $this->exec();
        $unique = array_unique($result);

        return count($unique) == 1 && $unique[0] === true;
    }
}