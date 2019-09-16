<?php
namespace cockroach\cache;

use cockroach\base\Cockroach;
use cockroach\base\Container;
use cockroach\exceptions\Exception;

/**缓存基类
 * Class Cache
 * @package cockroach\cache
 * @datetime 2019/9/16 11:32
 * @author roach
 * @email jhq0113@163.com
 */
abstract class Cache extends Cockroach implements ICacheItemPool
{
    /**
     * @var string
     * @datetime 2019/9/16 12:46
     * @author roach
     * @email jhq0113@163.com
     */
    public $prefix = 'cock:';

    /**
     * @var string
     * @datetime 2019/9/16 12:47
     * @author roach
     * @email jhq0113@163.com
     */
    public $cacheItemClass = 'cockroach\cache\CacheItem';

    /**
     * @var \SplStack
     * @datetime 2019/9/16 11:35
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_itemStack;

    /**
     * @param array $config
     * @datetime 2019/9/16 11:35
     * @author roach
     * @email jhq0113@163.com
     */
    public function init($config = [])
    {
        parent::init($config);
        $this->_itemStack = new \SplStack();
    }

    /**
     * @param string $key
     * @return CacheItem
     * @datetime 2019/9/16 13:33
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _createCache($key)
    {
        $item = Container::insure([
            'class'  => $this->cacheItemClass,
            'prefix' => $this->prefix
        ]);

        $item->assem([
            '_key' =>  $key
        ]);

        return $item;
    }

    /**
     * @param $valueItem
     * @return array|mixed
     * @datetime 2019/9/16 13:52
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _unserialize($valueItem)
    {
        return is_null($valueItem) ? $valueItem : igbinary_unserialize($valueItem);
    }

    /**
     * @param ICacheItem $item
     * @return string
     * @datetime 2019/9/16 13:53
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _serialize(ICacheItem $item)
    {
        return igbinary_serialize($item->get());
    }

    /**
     * @param string $key
     * @return CacheItem
     * @datetime 2019/9/16 13:11
     * @author roach
     * @email jhq0113@163.com
     */
    public function getItem($key)
    {
        $item = $this->_createCache($key);

        $valueItem = $this->_getValue($item->getKey());

        $item->assem([
            '_value' => $this->_unserialize($valueItem)
        ]);

        return $item;
    }

    /**
     * @param array $keys
     * @return array|\Traversable
     * @datetime 2019/9/16 13:59
     * @author roach
     * @email jhq0113@163.com
     */
    public function getItems(array $keys = array())
    {
        $items = [];
        $hashKeys = [];
        foreach ($keys as $key) {
            $item = $this->_createCache($key);

            array_push($hashKeys,$item->getKey());
            $items[ $key ] = $item;
        }

        $valueList = call_user_func_array([$this,'_mget'],$hashKeys);

        foreach ($items as $key => $item) {
            $hashKey = $item->getKey();
            if(isset($valueList[ $hashKey ])) {
                $items[ $key ] = $item->assem([
                    '_value' => $this->_unserialize($valueList[ $hashKey ])
                ]);
            }
        }

        return $items;
    }

    /**
     * @param string $key
     * @return bool|mixed
     * @datetime 2019/9/16 13:44
     * @author roach
     * @email jhq0113@163.com
     */
    public function hasItem($key)
    {
        return $this->_exists($this->_createCache($key)->getKey());
    }

    /**
     * @return bool
     * @throws Exception
     * @datetime 2019/9/16 13:43
     * @author roach
     * @email jhq0113@163.com
     */
    public function clear()
    {
        throw new Exception('sorry, not support!');
    }

    /**
     * @param string $key
     * @return bool
     * @datetime 2019/9/16 13:42
     * @author roach
     * @email jhq0113@163.com
     */
    public function deleteItem($key)
    {
        return $this->_delete($this->_createCache($key)->getKey());
    }

    /**
     * @param array $keys
     * @return bool
     * @datetime 2019/9/16 13:42
     * @author roach
     * @email jhq0113@163.com
     */
    public function deleteItems(array $keys)
    {
        $keys = array_map(function ($key){
            return $this->_createCache($key)->getKey();
        },$keys);

        return call_user_func_array([$this,'_delete'],$keys);
    }

    /**
     * @param ICacheItem $item
     * @return bool
     * @datetime 2019/9/16 13:12
     * @author roach
     * @email jhq0113@163.com
     */
    public function save(ICacheItem $item)
    {
        return $this->_saveValue($item->getKey(),$this->_serialize($item),$item->getTimeOut());
    }

    /**入栈
     * @param ICacheItem $item
     * @return bool
     * @datetime 2019/9/16 13:18
     * @author roach
     * @email jhq0113@163.com
     */
    public function saveDeferred(ICacheItem $item)
    {
        $this->_itemStack->push($item);
        return true;
    }

    /**
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

        while(!$this->_itemStack->isEmpty()){
            $isSave = $this->save($this->_itemStack->pop());
            if(!$isSave) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string   $key
     * @return mixed
     * @datetime 2019/9/16 12:56
     * @author roach
     * @email jhq0113@163.com
     */
    abstract protected function _getValue($key);

    /**
     * @param string $key1
     * @param string $key2
     * @param string $keyN
     * @return mixed
     * @datetime 2019/9/16 13:45
     * @author roach
     * @email jhq0113@163.com
     */
    abstract protected function _mget($key1);

    /**
     * @param string $key
     * @return mixed
     * @datetime 2019/9/16 13:43
     * @author roach
     * @email jhq0113@163.com
     */
    abstract protected function _exists($key);

    /**支持多key
     * @param string $key1
     * @param string $key2
     * @param string $keyN
     * @return bool
     * @datetime 2019/9/16 13:39
     * @author roach
     * @email jhq0113@163.com
     */
    abstract protected function _delete($key1);

    /**
     * @param string $key
     * @param string $value
     * @param int    $timeout
     * @return mixed
     * @datetime 2019/9/16 14:22
     * @author roach
     * @email jhq0113@163.com
     */
    abstract protected function _saveValue($key,$value,$timeout);
}