<?php
/**
 * Created by PhpStorm.
 * User: Jiang Haiqiang
 * Date: 2019/10/2
 * Time: 10:38 AM
 */

namespace cockroach\client;

use cockroach\base\Cockroach;
use cockroach\base\Container;
use cockroach\exceptions\ConfigException;
use cockroach\exceptions\RuntimeException;
use cockroach\extensions\EArray;
use cockroach\extensions\EUpstream;

/**
 * Class Micro
 * @package cockroach\client
 * @datetime 2019/10/2 10:39 AM
 * @author roach
 * @email jhq0113@163.com
 */
class Micro extends Cockroach
{
    /**
     * @var array
     * @datetime 2019/10/2 11:36 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $servers = [];

    /**数据中心
     * @var string
     * @datetime 2019/10/2 10:42 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $dc          = "dc1";

    /**节点名字
     * @var string
     * @datetime 2019/10/2 10:42 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $node;

    /**服务名称
     * @var string
     * @datetime 2019/10/2 10:42 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $name;

    /**lan地址
     * @var string
     * @datetime 2019/10/2 10:43 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $address;

    /**wan地址，空字符串表示不支持
     * @var string
     * @datetime 2019/10/2 10:43 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $wan;

    /**服务端口
     * @var int
     * @datetime 2019/10/2 10:43 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $port        = 888;

    /**服务路径
     * @var string
     * @datetime 2019/10/2 10:43 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $path        = "";

    /**协议名称
     * @var string
     * @datetime 2019/10/2 10:44 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $protocol;

    /**方法列表
     * @var array
     * @datetime 2019/10/2 10:44 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $funcs       = [];

    /**
     * @var array
     * @datetime 2019/10/2 10:47 AM
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_defaultProtocolList = [
      'swoole-boot' => [
          'class'       => 'cockroach\client\SwooleBoot',
          'serializeId' => '2'
      ]
    ];

    /**使用wan地址
     * @var bool
     * @datetime 2019/10/2 11:13 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $useWan = false;

    /**
     * @var array
     * @datetime 2019/10/2 10:49 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $protocolList = [];

    /**
     * @var Client
     * @datetime 2019/10/2 11:01 AM
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_client;

    /**
     * @param array $config
     * @datetime 2019/10/2 10:50 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function init($config = [])
    {
        parent::init($config);
        $this->protocolList = EArray::merge($this->_defaultProtocolList, $this->protocolList);
    }

    /**选择服务器,根据Ip使用一致性哈希算法
     * @param array $servers
     * @return array
     * @datetime 2019/10/2 11:44 AM
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _selectServer($servers)
    {
        $field = $this->useWan ? 'wan' : 'address';
        return EUpstream::consistentHash($servers,$field);
    }

    /**
     * @return Client
     * @throws ConfigException
     * @datetime 2019/10/2 11:16 AM
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _client()
    {
        if(!is_null($this->_client)) {
            return $this->_client;
        }

        if(!isset($this->protocol) && !empty($this->servers)) {
            $config = $this->_selectServer($this->servers);
            $this->assemInsure($config);
        }

        if(!isset($this->protocolList[ $this->protocol ])) {
            throw new ConfigException("还未支持协议[{$this->protocol}]");
        }

        $config = $this->protocolList[ $this->protocol ];
        $config = array_merge($config,[
            'host' => $this->useWan ? $this->wan :$this->address,
            'port' => $this->port,
            'path' => $this->path
        ]);

        $this->_client = Container::insure($config,'cockroach\client\SwooleBoot');

        return $this->_client;
    }

    /**
     * @param string $funcName
     * @param array  $params
     * @return mixed
     * @throws ConfigException
     * @throws RuntimeException
     * @datetime 2019/10/2 12:13 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function call($funcName,$params = [])
    {
        $client = $this->_client();

        if(!isset($this->funcs[ $funcName ])) {
            throw new RuntimeException("函数[{$funcName}]未注册");
        }

        if(is_string($this->funcs[ $funcName ])) {
            $this->funcs[ $funcName ] = empty($this->funcs[ $funcName ]) ? [] : json_decode($this->funcs[ $funcName ],true);
        }

        $route = $this->funcs[ $funcName ]['route'] ?? $funcName;

        return $client->call($route, $params);
    }
}