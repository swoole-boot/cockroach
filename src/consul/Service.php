<?php
namespace cockroach\consul;

use cockroach\exceptions\RuntimeException;

/**
 * Class Service
 * @package cockroach\consul
 * @datetime 2019/8/31 1:09 PM
 * @author roach
 * @email jhq0113@163.com
 */
class Service
{
    /**
     * @var string $id
     * @datetime 2019/8/31 1:09 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $id = '';

    /**数据中心
     * @var string $dataCenter
     * @datetime 2019/8/31 1:09 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $dataCenter;

    /**节点名称
     * @var string $node
     * @datetime 2019/8/31 1:09 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $node;

    /**节点host地址
     * @var string $address
     * @datetime 2019/8/31 1:09 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $address;

    /**节点meta
     * @var array $nodeMeta
     * @datetime 2019/8/31 1:09 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $nodeMeta = [
        'cock_regisTool' => 'cockroach'
    ];

    /**服务id
     * @var string $serviceId
     * @datetime 2019/8/31 1:09 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $serviceId = '';

    /**服务名称
     * @var string $serviceName
     * @datetime 2019/8/31 1:09 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $serviceName = '';

    /**
     * @return string
     * @datetime 2019/8/31 1:09 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function getServiceId()
    {
        return $this->serviceId ?: $this->serviceAddress.':'.$this->servicePort;
    }

    /**服务tags
     * @var array $tags
     * @datetime 2019/8/31 1:13 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $tags = [];

    /**服务host地址
     * @var string $serviceAddress
     * @datetime 2019/8/31 1:13 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $serviceAddress = '';

    /**服务端口
     * @var integer $servicePort
     * @datetime 2019/8/31 1:13 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $servicePort;

    /**服务meta
     * @var array
     * @datetime 2019/8/31 1:13 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $serviceMeta = [
        'cock_regisTool' => 'cockroach'
    ];

    /**
     * @var bool
     * @datetime 2019/8/31 1:13 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $skipNodeUpdate = false;

    /**服务健康监测
     * @var array $check
     * @datetime 2019/8/31 1:13 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $check = [];

    /**
     * @return bool
     * @throws RuntimeException
     * @datetime 2019/8/31 1:13 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function ensureAttribute()
    {
        if(empty($this->address) && empty($this->serviceAddress)) {
            throw new RuntimeException('address和serviceAddress至少有一个必填');
        }

        if(!isset($this->servicePort)) {
            throw new RuntimeException('servicePort必填');
        }

        if(empty($this->serviceName)) {
            throw new RuntimeException('serviceName必填');
        }

        if(empty($this->node)) {
            throw new RuntimeException('node必填');
        }

        return true;
    }

    /**
     * @return array
     * @datetime 2019/8/31 1:13 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function toArray()
    {
        $this->address        = $this->address ?: $this->serviceAddress;
        $this->serviceAddress = $this->serviceAddress ?: $this->address;

        return [
            'ID'                =>  $this->id,
            'Datacenter'        =>  $this->dataCenter,
            'Node'              =>  $this->node,
            'Address'           =>  $this->address,
            'NodeMeta'          => $this->nodeMeta,
            'Service'           => [
                'Id'        => $this->getServiceId(),
                'Service'   => $this->serviceName,
                'tags'      => $this->tags,
                'Address'   => $this->serviceAddress ?: $this->address ,
                'Port'      => $this->servicePort,
                'Meta'      => $this->serviceMeta
            ],
            'Check' => array_merge([
                'Node'          => $this->node,
                'ServiceID'     => $this->serviceId
            ],$this->check),
            'SkipNodeUpdate' => $this->skipNodeUpdate
        ];
    }

    /**
     * @param array $config
     * @return $this
     * @datetime 2019/8/31 1:12 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function blockByArray(array $config)
    {
        $this->id               = $config['ID'];
        $this->node             = $config['Node'];
        $this->address          = $config['Address'];
        $this->dataCenter       = $config['Datacenter'];
        $this->nodeMeta         = $config['NodeMeta'];
        $this->serviceId        = $config['ServiceID'];
        $this->serviceName      = $config['ServiceName'];
        $this->tags             = $config['ServiceTags'];
        $this->serviceAddress   = $config['ServiceAddress'];
        $this->serviceMeta      = $config['ServiceMeta'];
        $this->servicePort      = $config['ServicePort'];
        $this->skipNodeUpdate   = $config['ServiceEnableTagOverride'];

        return $this;
    }
}