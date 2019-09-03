<?php
namespace cockroach\extensions;

use cockroach\base\Extension;

/**
 * Class EUpstream
 * @package cockroach\extensions
 * @datetime 2019/8/31 2:56 PM
 * @author roach
 * @email jhq0113@163.com
 */
class EUpstream extends Extension
{
    /**轮询
     * @param array $servers
     * @return mixed
     * @datetime 2019/8/31 2:58 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function shuffle(array $servers)
    {
        shuffle($servers);
        return $servers[0];
    }

    /**按权重
     * @param array $servers
     * @param string $field
     * @return mixed
     * @datetime 2019/8/31 4:21 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function weight(array &$servers,$field = 'weight')
    {
        $weight = 0;

        $weightList = [];

        foreach ($servers as $index => $server) {
            $weight += $server[ $field ];
            for ($i = 0; $i < $server['weight']; $i++) {
                array_push($weightList,$index);
            }
        }

        $index = $weightList[ mt_rand(0, $weight -1) ];
        return $servers[ $index ];
    }

    /**一致性hash,此方法必须运行在64位以上的机器上
     * @param array $servers
     * @param string $field
     * @param null $key
     * @return mixed
     * @datetime 2019/8/31 3:16 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function consistentHash(array &$servers, $field = 'host',$key = null)
    {
        $positions = [];
        foreach ($servers as $index => $server) {
            $positions[ crc32($server[ $field ]) ] = $index;
        }

        //排序
        ksort($positions);

        //默认key为客户端ip
        $key = is_null($key) ? EHttp::getClientIp() : $key;

        //计算key的hash值
        $currentHash = crc32($key);

        //上一个点
        $lastPosition = 0;
        //寻找hash值最近的节点
        foreach ($positions as $position => $index) {
            //找到小于自己的点
            if($currentHash <= $position) {
                //没有比自己小的点
                if($lastPosition == 0) {
                    return $servers[ $index ];
                }

                //当前点到小点距离
                $lowDis = $currentHash - $lastPosition;
                //当前点到大点距离
                $upDis  = $position    - $currentHash;

                //如果小点距离小于大点，则用小点
                if($lowDis < $upDis) {
                    $index = $positions[ $lastPosition ];
                }

                return $servers[ $index ];
            } else {
                $lastPosition = $position;
            }
        }

        //没有比自己大的点，则用第一个
        return $servers[ 0 ];
    }
}