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

/**
 * Class Micro
 * @package cockroach\client
 * @datetime 2019/10/2 10:39 AM
 * @author roach
 * @email jhq0113@163.com
 */
class Micro extends Cockroach
{
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
    public $node        = "";

    /**服务名称
     * @var string
     * @datetime 2019/10/2 10:42 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $name        = "";

    /**lan地址
     * @var string
     * @datetime 2019/10/2 10:43 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $address     = "";

    /**wan地址，空字符串表示不支持
     * @var string
     * @datetime 2019/10/2 10:43 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $wan         = "";

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
    public $protocol    = "";

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
          'class' => 'cockroach\client\SwooleBoot'
      ]
    ];

    /**使用wan地址
     * @var bool
     * @datetime 2019/10/2 11:13 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $useWan = false;

    /**调用前校验func是否存在
     * @var bool
     * @datetime 2019/10/2 11:14 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $validateFunc = true;

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
     * @param string $func
     * @param array  $params
     * @return mixed
     * @throws ConfigException
     * @throws RuntimeException
     * @datetime 2019/10/2 11:15 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function call($func,$params = [])
    {
        if($this->validateFunc && !isset($this->funcs[ $func ])) {
            throw new RuntimeException("函数[{$func}]未注册");
        }

        return $this->_client()->call($func, $params);
    }
}