<?php
namespace cockroach\cache;

use cockroach\base\Cockroach;

/**分级缓存
 * Class Level
 * @package cockroach\cache
 * @datetime 2019/9/17 10:23
 * @author roach
 * @email jhq0113@163.com
 */
class Level extends Cockroach
{
    /**Redis缓存
     * @var Redis
     * @datetime 2019/9/17 10:23
     * @author roach
     * @email jhq0113@163.com
     */
    public $redis = [];

    /**本地缓存，如:Yac
     * @var Cache
     * @datetime 2019/9/17 10:23
     * @author roach
     * @email jhq0113@163.com
     */
    public $local = [];

    /**
     * @param mixed $value
     * @param int   $timeout
     * @return string
     * @datetime 2019/9/17 14:14
     * @author roach
     * @email jhq0113@163.com
     */
    public function encode($value,$timeout)
    {
        return igbinary_serialize([
            'value'     => $value,
            'expireAt'  => time()+ $timeout
        ]);
    }

    /**
     * @param string $data
     * @return array
     * @datetime 2019/9/17 14:15
     * @author roach
     * @email jhq0113@163.com
     */
    public function decode($data)
    {
        if(empty($data)) {
            return [
                'value'     => null,
                'expireAt'  => 0
            ];
        }

        return igbinary_unserialize($data);
    }

    /**通用缓存
     * @param string   $key                 缓存Key
     * @param callable $workHandler         业务回调，local和redis都没有查询到的时候调用
     * @param int      $timeout             缓存时效，默认60秒
     * @param int      $lockTimeout         分布式锁超时时间,默认8秒
     * @return mixed
     * @datetime 2019/9/17 14:08
     * @author roach
     * @email jhq0113@163.com
     */
    public function get($key, callable $workHandler, $timeout = 60, $lockTimeout = 8)
    {
        $item = $this->local->getItem($key);
        $time = time();
        $oldValue = null;

        //本地缓存命中
        if($item->isHit()) {
           $data = $this->decode($item->get());
           $oldValue = $data['value'];

           //缓存有效，最理想情况
           if($data['expireAt'] > $time) {
               return $oldValue;
           }
        }

        //去redis缓存查询
        $item = $this->redis->getItem($key);

        //redis缓存命中
        if($item->isHit()) {
            $data = $this->decode($item->get());

            //缓存有效，比较理想情况
            if($data['expireAt'] > $time) {
                //更新到本地缓存
                $this->local->save($item);
                return $data['value'];
            }

            $oldValue = is_null($data['value']) ? $oldValue : $data['value'];
        }

        $lockKey = 'lock:'.$key;
        //local和redis中都没有
        $token = $this->redis->lock($lockKey,$lockTimeout);

        //没有获得锁且有历史数据,将历史值返回，一旦存储过数据，数据是永久存储的，之后的oldValue均会有值
        if($token === false && !is_null($oldValue)) {
            return $oldValue;
        }

        //获得锁，执行耗时操作
        $value = call_user_func($workHandler);

        $item = $item->assem([
            '_value' => $this->encode($value,$timeout)
        ]);

        //更新local
        $this->local->save($item);
        //更新redis
        $this->redis->save($item);

        //释放锁
        $this->redis->unlock($lockKey,$token);

        return $value;
    }
}