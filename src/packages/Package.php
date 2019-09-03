<?php
namespace cockroach\packages;

use cockroach\base\Cockroach;

/**
 * Class Package
 * @package cockroach\packages
 * @datetime 2019/8/31 11:53 PM
 * @author roach
 * @email jhq0113@163.com
 */
abstract class Package extends Cockroach
{
    /**序列化方式ID
     * @var string
     * @datetime 2019/9/1 12:43 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $serializeId = '1';

    /**
     * @var int
     * @datetime 2019/8/31 11:54 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $headerSize;

    /**
     * @var string
     * @datetime 2019/8/31 11:54 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $headerUnpackFormat;

    /**
     * @var string
     * @datetime 2019/8/31 11:54 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $headerPackFormat;

    const SERIALIZE_JSON       = '1';
    const SERIALIZE_BINARY     = '2';
    const SERIALIZE_PHP        = '3';
    const SERIALIZE_MSGPACK    = '4';

    /**序列化
     * @param string  $type
     * @param string  $data
     * @return string
     * @datetime 2019/9/1 12:18 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function serialize($type,$data)
    {
        switch ($type) {
            case self::SERIALIZE_JSON:
                return json_encode($data,JSON_UNESCAPED_UNICODE);
            case self::SERIALIZE_BINARY:
                return igbinary_serialize($data);
            case self::SERIALIZE_PHP:
                return serialize($data);
            case self::SERIALIZE_MSGPACK:
                return msgpack_pack($data);
            default:
                return json_encode($data,JSON_UNESCAPED_UNICODE);
        }
    }

    /**发序列化
     * @param string $type
     * @param mixed  $data
     * @return mixed|string
     * @datetime 2019/9/1 12:21 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function unserialize($type,$data)
    {
        switch ($type) {
            case self::SERIALIZE_JSON:
                return json_decode($data,true);
            case self::SERIALIZE_BINARY:
                return igbinary_unserialize($data);
            case self::SERIALIZE_PHP:
                return unserialize($data);
            case self::SERIALIZE_MSGPACK:
                return msgpack_unpack($data);
            default:
                return json_encode($data,true);
        }
    }

    /**pack包头
     * @param array $header
     * @return mixed
     * @datetime 2019/8/31 11:55 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function packHeader($header)
    {
        array_unshift($header,$this->headerPackFormat);
        return call_user_func_array('pack',$header);
    }

    /**unpack包头
     * @param string $data
     * @return array
     * @datetime 2019/8/31 11:56 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function unpackHeader($data)
    {
        $header = substr($data,0,$this->headerSize);

        return unpack($this->headerUnpackFormat,$header,0);
    }

    /**封包
     * @param mixed $params
     * @return mixed
     * @datetime 2019/8/31 11:59 PM
     * @author roach
     * @email jhq0113@163.com
     */
    abstract public function pack($params);

    /**解包
     * @param string $data
     * @return mixed
     * @datetime 2019/8/31 11:59 PM
     * @author roach
     * @email jhq0113@163.com
     */
    abstract public function unpack($data);

}