<?php
namespace cockroach\extensions;

/**
 * Class EReturn
 * @package cockroach\extensions
 * @datetime 2019/8/30 18:19
 * @author roach
 * @email jhq0113@163.com
 */
class EReturn
{
    /**
     * 操作成功
     */
    const SUCCESS      = '20000';

    /**
     * 参数错误
     */
    const ERROR_PARAMS = '40000';

    /**
     * 服务器内部错误
     */
    const ERROR_INNER  = '50000';

    /**错误响应
     * @param string $message
     * @param string $code
     * @param mixed  $data
     * @return array
     * @datetime 2019/8/30 18:26
     * @author roach
     * @email jhq0113@163.com
     */
    static public function error($message,$code = self::ERROR_INNER,$data= null)
    {
        return [
            'code'   => $code,
            'message'  => $message,
            'data'     => $data ?: (object)[]
        ];
    }

    /**成功响应
     * @param mixed   $data
     * @return array
     * @datetime 2019/8/30 18:26
     * @author roach
     * @email jhq0113@163.com
     */
    static public function success($data = [])
    {
        return self::error('success',self::SUCCESS,$data);
    }

    /**是否为成功响应
     * @param array $errorConfig
     * @return bool
     * @datetime 2019/8/30 18:25
     * @author roach
     * @email jhq0113@163.com
     */
    static public function isSuccess($errorConfig)
    {
        return isset($errorConfig['code']) && $errorConfig['code'] == self::SUCCESS;
    }
}