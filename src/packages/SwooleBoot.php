<?php
/**
 * Created by PhpStorm.
 * User: Jiang Haiqiang
 * Date: 2019/9/1
 * Time: 12:00 AM
 */

namespace cockroach\packages;

/**
 * Class SwooleBoot
 * @package cockroach\packages
 * @datetime 2019/9/1 12:00 AM
 * @author roach
 * @email jhq0113@163.com
 */
class SwooleBoot extends Package
{
    /**
     * @var int
     * @datetime 2019/9/1 12:00 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $headerSize = 69;

    /**
     * @var string $headerStruct
     * @datetime 2019/9/1 12:06 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $headerUnpackFormat = "NBodyLen/CSerialize/a32Sign/a19Datetime/a13Option";

    /**
     * @var string $headerPack
     * @datetime 2019/9/1 12:06 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $headerPackFormat   = "NCa32a19a13";

    /**按指定序列化方式封包
     * @param mixed  $params
     * @param string $serializeId
     * @return string
     * @datetime 2019/9/12 13:48
     * @author roach
     * @email jhq0113@163.com
     */
    public function packBySerializeId($params,$serializeId)
    {
        $body = $this->serialize($serializeId,$params);

        $header = [
            strlen($body),
            $serializeId,
            md5($body),
            date('Y-m-d H:i:s'),
            str_repeat('1',13)
        ];

        return $this->packHeader($header).$body;
    }

    /**封包
     * @param mixed $params
     * @return string
     * @datetime 2019/9/1 12:05 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function pack($params)
    {
        $body = $this->serialize($this->serializeId,$params);

        $header = [
            strlen($body),
            $this->serializeId,
            md5($body),
            date('Y-m-d H:i:s'),
            str_repeat('1',13)
        ];

        return $this->packHeader($header).$body;
    }

    /**解包
     * @param string $data
     * @return array
     * @datetime 2019/9/1 12:01 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function unpack($data)
    {
        $result = [
            'data'   => ''
        ];

        $length = strlen($data);

        if($length > $this->headerSize) {
            $header = $this->unpackHeader($data);
            //解析包头失败
            if(!isset($header['BodyLen'],$header['Serialize'],$header['Sign'])) {
                return $result;
            }

            //包不完整
            if(($length - $this->headerSize) != $header['BodyLen']) {
                return $result;
            }

            $body = substr($data,$this->headerSize);

            //签名未过
            if(md5($body) !== $header['Sign']) {
                return $result;
            }

            $result['header'] = $header;
            $result['data']   = $this->unserialize($header['Serialize'],$body);
        }

        return $result;
    }
}