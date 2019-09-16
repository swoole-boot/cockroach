<?php
namespace cockroach\cache;

use cockroach\exceptions\Exception;
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

    /**
     * @param string $key
     * @return string
     * @datetime 2019/9/16 16:47
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _getValue($key)
    {
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
        return $this->_client()->exists($key);
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
        return call_user_func_array([ $this->_client(), 'delete' ],func_get_args());
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
            return $this->_client()->set($key,$value);
        }
        return $this->_client()->set($key,$value,$timeout);
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
}