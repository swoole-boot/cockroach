<?php
namespace cockroach\cache;

/**
 * Class CacheItem
 * @package cockroach\cache
 * @datetime 2019/9/16 11:18
 * @author roach
 * @email jhq0113@163.com
 */
class CacheItem implements ICacheItem
{
    /**
     * @var string
     * @datetime 2019/9/16 11:19
     * @author roach
     * @email jhq0113@163.com
     */
    public $prefix = '';

    /**
     * @var string
     * @datetime 2019/9/16 11:36
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_key;

    /**
     * @var mixed
     * @datetime 2019/9/16 11:40
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_value = null;

    /**
     * @var bool
     * @datetime 2019/9/16 11:44
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_isHit = false ;

    /**单位秒，默认值0，代表永不过期
     * @var int
     * @datetime 2019/9/16 11:40
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_expireAt = 0;

    /**
     * @var int
     * @datetime 2019/9/16 14:24
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_timeout = 0;

    /**批量赋值
     * @param array $config
     * @return $this
     * @datetime 2019/9/16 14:30
     * @author roach
     * @email jhq0113@163.com
     */
    public function assem($config = [])
    {
        foreach ($config as $property => $value) {
            if(property_exists($this,$property)) {
                $this->$property = $value;
            }
        }
        return $this;
    }

    /**
     * @return string
     * @datetime 2019/9/16 11:55
     * @author roach
     * @email jhq0113@163.com
     */
    public function getKey()
    {
        $key = $this->prefix . $this->_key;

        if(strlen($key) > 32) {
            return md5($key);
        }

        return $key;
    }

    /**
     * @return mixed
     * @datetime 2019/9/16 11:56
     * @author roach
     * @email jhq0113@163.com
     */
    public function get()
    {
        return $this->_value;
    }

    /**
     * @param mixed $value
     * @return $this
     * @datetime 2019/9/16 12:41
     * @author roach
     * @email jhq0113@163.com
     */
    public function set($value)
    {
        $this->_value = $value;
        return $this;
    }

    /**
     * @return bool
     * @datetime 2019/9/16 11:52
     * @author roach
     * @email jhq0113@163.com
     */
    public function isHit()
    {
        return $this->_isHit;
    }

    /**
     * @param int
     * @return $this|ICacheItem
     * @datetime 2019/9/16 12:38
     * @author roach
     * @email jhq0113@163.com
     */
    public function expiresAfter($time)
    {
        $this->_timeout = $time;

        return $this;
    }

    /**不允许设置为小于1的数字，会自动转换为1
     * @param int
     * @return ICacheItem|string
     * @datetime 2019/9/16 11:57
     * @author roach
     * @email jhq0113@163.com
     */
    public function expiresAt($expiration)
    {
        $this->_expireAt = ($expiration < 1) ? 1 : $expiration;
        return $this;
    }

    /**
     * @return int
     * @datetime 2019/9/16 14:25
     * @author roach
     * @email jhq0113@163.com
     */
    public function getTimeOut()
    {
        if($this->_timeout > 0) {
            return $this->_timeout;
        }

        if($this->_expireAt == 0) {
            return 0;
        }

        $timeout = $this->_expireAt - time();
        return $timeout > 0 ? $timeout : 1;
    }
}