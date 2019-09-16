<?php
namespace cockroach\cache;

/**
 * Class Yac
 * @package cockroach\cache
 * @datetime 2019/9/16 11:33
 * @author roach
 * @email jhq0113@163.com
 */
class Yac extends Cache
{
    /**
     * @var \Yac
     * @datetime 2019/9/16 14:06
     * @author roach
     * @email jhq0113@163.com
     */
    private $_client;

    /**
     * @return \Yac
     * @datetime 2019/9/16 14:08
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _client()
    {
        if($this->_client instanceof \Yac) {
            return $this->_client;
        }

        $this->_client = new \Yac('');
        return $this->_client;
    }

    /**
     * @param string $key
     * @return mixed|void
     * @datetime 2019/9/16 14:08
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
     * @return mixed|void
     * @datetime 2019/9/16 14:13
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _mget($key1)
    {
        return $this->_client()->get(func_get_args());
    }

    /**
     * @param string $key
     * @return bool
     * @datetime 2019/9/16 14:15
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _exists($key)
    {
        $value = $this->_client()->get($key);

        return $value !== false;
    }

    /**
     * @param string $key1
     * @return bool
     * @datetime 2019/9/16 14:17
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _delete($key1)
    {
        return $this->_client()->delete(func_get_args());
    }

    /**
     * @param string $key
     * @param string $value
     * @param int    $timeout
     * @return bool
     * @datetime 2019/9/16 14:33
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _saveValue($key, $value,$timeout)
    {
        if($timeout == 0) {
            return $this->_client()->set($key,$value);
        }

        return $this->_client()->set($key,$value,$timeout);
    }

    /**
     * @return bool
     * @datetime 2019/9/16 15:41
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _flush()
    {
        return $this->_client()->flush();
    }

    /**
     * @return array
     * @datetime 2019/9/16 15:41
     * @author roach
     * @email jhq0113@163.com
     */
    public function info()
    {
        return $this->_client()->info();
    }
}