<?php
namespace cockroach\consul;

use cockroach\base\Cockroach;
use cockroach\extensions\EHttp;

/**
 * Class Client
 * @package cockroach\consul
 * @datetime 2019/8/31 1:15 PM
 * @author roach
 * @email jhq0113@163.com
 */
class Client extends Cockroach
{
    /**备用节点
     * @example http://10.20.76.58:8500
     * @var string $standbyNode
     * @datetime 2019/8/31 1:16 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $standbyNode;

    /**本机节点
     * @var string
     * @datetime 2019/8/31 1:16 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $localNode = 'http://127.0.0.1:8500';

    /**数据中心
     * @var string
     * @datetime 2019/8/31 1:16 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $dataCenter = 'dc1';

    /**
     * @var string
     * @datetime 2019/8/31 1:16 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $token;

    /**向agent节点发送请求
     * @param string $method
     * @param string $path
     * @param array  $params
     * @param array  $header
     * @return array
     * @datetime 2019/8/31 2:03 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function request($method,$path,$params=[],$header=[])
    {
        $url = $this->localNode.$path;

        //附加token
        if(isset($this->token)) {
            array_push($header,'X-Consul-Token:'.$this->token);
        }

        $response = EHttp::request($method,$url,$params,$header);

        \cockroach\extensions\EFile::write('/tmp/logs/regis.log',json_encode($response).PHP_EOL);

        if($response['info']['http_code'] === 0 && !empty($this->standbyNode)) {
            $url = $this->standbyNode.$path;
            $response = EHttp::request($method,$url,$params,$header);
        }

        return $response;
    }

    /**注册服务
     * @param Service $service               服务对象
     * @return bool
     * @throws \cockroach\exceptions\RuntimeException
     * @datetime 2019/8/31 2:02 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function register(Service $service)
    {
        if(!isset($service->dataCenter)) {
            $service->dataCenter = $this->dataCenter;
        }

        $service->ensureAttribute();

        $response = $this->request('put','/v1/catalog/register',json_encode($service->toArray(),true));

        return EHttp::requestSuccess($response) && $response['body'] === 'true';
    }

    /**注销服务
     * @param string  $node              节点名称
     * @param string  $serviceId         服务id
     * @param string  $dataCenter        数据中心，默认为当前数据中心，可不传
     * @return bool
     * @datetime 2019/8/31 2:02 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function deregister($node,$serviceId,$dataCenter = null)
    {
        $params = [
            'Datacenter'    =>  is_null($dataCenter) ? $this->dataCenter : $dataCenter,
            'Node'          =>  $node,
            'ServiceID'     =>  $serviceId,
        ];

        $response = $this->request('put','/v1/catalog/deregister',json_encode($params,true));

        return EHttp::requestSuccess($response) && $response['body'] === 'true';
    }

    /**通过服务注销服务
     * @param Service $service
     * @return bool
     * @datetime 2019/8/31 2:02 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function deregisterByService(Service $service)
    {
        return $this->deregister($service->node,$service->getServiceId(),$service->dataCenter ?: $this->dataCenter );
    }

    /**发现服务
     * @param string $serviceName      服务名称
     * @param bool   $convert2Object   是否将服务列表转换为base\consul\Service对象列表，默认转换
     * @return array
     * @datetime 2019/8/31 2:01 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function discover($serviceName,$convert2Object = true)
    {
        $response = $this->request('get','/v1/catalog/service/'.$serviceName);
        if(EHttp::requestSuccess($response)) {
            $serviceList = json_decode($response['body'],true);
            if(!$convert2Object) {
                return $serviceList;
            }

            if(!empty($serviceList)) {
                $services = [];
                foreach ($serviceList as $service) {
                    $item = new Service();
                    $item->blockByArray($service);
                    array_push($services,$item);
                }

                return $services;
            }
        }

        return [];
    }

    /**获取key信息，返回数组
     * @param string $key       键
     * @param array  $params    参数
     * @param bool   $decrypt   值是否自动解密，默认解密
     * @return array
     * @datetime 2019/8/31 2:01 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function kvGet($key, $params=[], $decrypt = true)
    {
        $params = array_merge([
            'dc'      => $this->dataCenter
        ],$params);

        $response = $this->request('get','/v1/kv/'.$key,$params);

        if(EHttp::requestSuccess($response) && !empty($response['body'])) {
            $values = json_decode($response['body'],true);;

            if($decrypt) {
                foreach ($values as &$value) {
                    $value['Value'] = base64_decode($value['Value']);
                }
            }

            return $values;
        }
        return [];
    }

    /**添加或修改kv
     * @param string  $key          键
     * @param string  $value        值
     * @param array   $params       http协议queryString参数
     * @return bool
     * @datetime 2019/8/31 2:01 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function kvSet($key,$value = null,$params=[])
    {
        $params = array_merge([
            'dc'      => $this->dataCenter
        ],$params);
        $response = $this->request('put','/v1/kv/'.$key.'?'.http_build_query($params),$value);
        return EHttp::requestSuccess($response) && $response['body'] === 'true';
    }

    /**创建目录
     * @param string $directory  目录名字
     * @return bool
     * @datetime 2019/8/31 2:01 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function kvCreateDir($directory)
    {
        if(substr($directory,-1,1) !== '/') {
            $directory.='/';
        }

        return $this->kvSet($directory);
    }

    /**获取key值，返回字符串
     * @param string $key    键名
     * @return string
     * @datetime 2019/8/31 2:01 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function kvGetValue($key)
    {
        $value = $this->kvGet($key);
        if(empty($value)) {
            return '';
        }

        return $value[0]['Value'];
    }

    /**获取目录下所有的键值对
     * @param string  $directory  目录名
     * @return array
     * @datetime 2019/8/31 2:00 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function kvGetRecurse($directory)
    {
        $values = $this->kvGet($directory,[
            'recurse' => 1
        ]);

        if(!empty($values)) {
            return array_column($values,'Value','Key');
        }

        return $values;
    }

    /**删除指定key
     * @param string $key        键名
     * @param array  $params     http协议queryString参数
     * @return bool
     * @datetime 2019/8/31 1:44 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function kvDelete($key,$params=[])
    {
        $params = array_merge([
            'dc'      => $this->dataCenter
        ],$params);
        $response = $this->request('delete','/v1/kv/'.$key.'?'.http_build_query($params));
        return EHttp::requestSuccess($response) && $response['body'] === 'true';
    }

    /**删除目录
     * @param string $directory   目录名
     * @return bool
     * @datetime 2019/8/31 1:43 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function kvDeleteDir($directory)
    {
        return $this->kvDelete($directory,[
            'recurse' => 1
        ]);
    }

}