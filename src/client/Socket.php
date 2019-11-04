<?php
namespace cockroach\client;

use cockroach\exceptions\ConfigException;

/**
 * Class Socket
 * @package cockroach\client
 * @datetime 2019/9/13 16:36
 * @author roach
 * @email jhq0113@163.com
 */
class Socket extends Client
{
    /**
     * @var \cockroach\packages\SwooleBoot
     * @datetime 2019/9/13 16:31
     * @author roach
     * @email jhq0113@163.com
     */
    public $packager = [];

    /**
     * @var string
     * @datetime 2019/9/13 16:51
     * @author roach
     * @email jhq0113@163.com
     */
    public $protocal = 'tcp';

    /**
     * @return resource | null
     * @datetime 2019/9/13 16:51
     * @author roach
     * @email jhq0113@163.com
     */
    public function createClient()
    {
        $address = $this->protocal.'://'.$this->host.':'.$this->port;
        $fp = stream_socket_client($address,$errno,$errstr,$this->timeout);
        if(!$fp) {
            return null;
        }

        return $fp;
    }

    /**
     * @param string $data
     * @datetime 2019/9/13 16:54
     * @author roach
     * @email jhq0113@163.com
     */
    public function send($data)
    {
        $fp = $this->client();
        fwrite($fp,$data);
    }

    /**读包
     * @param resource $fp
     * @param int      $length
     * @return string
     * @datetime 2019/9/13 16:55
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _read($fp,$length)
    {
        $data = '';
        while ($length > 0) {
            $block = fread($fp,$length);
            if($block === false) {
                return $data;
            }

            $data .= $block;
            $length -= strlen($block);
        }

        return $data;
    }

    /**接包
     * @return array|string
     * @datetime 2019/9/13 16:59
     * @author roach
     * @email jhq0113@163.com
     */
    public function recv()
    {
        $fp = $this->client();

        $data = $this->_read($fp,$this->packager->headerSize);
        if(empty($data)) {
            return '';
        }

        $header = $this->packager->unpackHeader($data);
        if(isset($header['BodyLen'])) {
            $data .= $this->_read($fp,$header['BodyLen']);
            $result = $this->packager->unpack($data);

            if(isset($result['data'])) {
                return $result['data'];
            }
        }

        return '';
    }

    /**
     * @param array $params
     * @return array|mixed|string
     * @datetime 2019/9/13 17:03
     * @author roach
     * @email jhq0113@163.com
     */
    public function request($params = [])
    {
        $data = $this->packager->pack($params);
        $this->send($data);
        return $this->recv();
    }

    /**
     * @param string $func
     * @param array  $params
     * @return mixed|void
     * @throws ConfigException
     * @datetime 2019/9/13 17:09
     * @author roach
     * @email jhq0113@163.com
     */
    public function call($func,$params)
    {
        throw new ConfigException(__CLASS__.'没有实现call方法,需要使用其实现类');
    }
}