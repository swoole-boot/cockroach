<?php
namespace cockroach\extensions;

/**
 * Class EHttp
 * @package cockroach\extensions
 * @datetime 2019/8/30 18:42
 * @author roach
 * @email jhq0113@163.com
 */
class EHttp
{
    /**
     * @var string
     * @datetime 2019/8/30 18:42
     * @author roach
     * @email jhq0113@163.com
     */
    private static $_clientIp;

    /**获取客户端ip
     * @param bool $long
     * @return int|mixed|string
     * @datetime 2019/8/30 18:41
     * @author roach
     * @email jhq0113@163.com
     */
    public static function getClientIp( $long = false)
    {
        if(isset(self::$_clientIp)) {
            return $long ? ip2long(self::$_clientIp) : self::$_clientIp;
        }

        if(ECli::isCli()) {
            self::$_clientIp = '127.0.0.1';
            return $long ? ip2long(self::$_clientIp) : self::$_clientIp;
        }

        //如果经过了代理
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);

            self::$_clientIp = $ip[0];
        } else {
            self::$_clientIp = $_SERVER['REMOTE_ADDR'];
        }

        return $long ? ip2long(self::$_clientIp) : self::$_clientIp;
    }

    /**发送http请求
     * @param string $method
     * @param string $url
     * @param string $params
     * @param array  $header
     * @param array  $opts
     * @return mixed
     * @datetime 2019/8/30 19:12
     * @author roach
     * @email jhq0113@163.com
     */
    static public function request($method, $url, $params = '', array $header = [], array $opts = [])
    {
        //请求方法转换为大写
        $method = strtoupper($method);

        //默认opt
        $optArray = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CUSTOMREQUEST  => $method
        ];

        //是否携带参数
        if(!empty($params)) {
            if($method == 'GET') {
                $params = is_array($params) ? http_build_query($params) : $params;
                $url .= (strpos($url,'?') > 0) ? '&'.$params : '?'.$params;
            }else {
                $optArray[ CURLOPT_POSTFIELDS ] = $params;
            }
        }

        //设置请求url
        $optArray[ CURLOPT_URL ] = $url;

        //设置请求头
        if(!empty($header)) {
            $optArray[ CURLOPT_HTTPHEADER ] = $header;
        }

        //判断https
        if(substr($url,0,5) == 'https') {
            $optArray[ CURLOPT_SSL_VERIFYPEER ] = false;
            $optArray[ CURLOPT_SSL_VERIFYHOST ] = false;
        }

        //合并opts
        if(!empty($opts)) {
            $optArray = array_merge($optArray,$opts);
        }

        $ci = curl_init();
        curl_setopt_array($ci,$optArray);

        $response['body']     = curl_exec($ci);
        $response['info']     = curl_getinfo($ci);
        @curl_close($ci);

        return $response;
    }

    /**判断http请求是否请求成功
     * @param array $response
     * @return bool
     * @datetime 2019/8/31 2:07 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function requestSuccess($response)
    {
        return $response['info']['http_code'] == '200';
    }

    /***发送get请求
     * @param string $url
     * @param array  $header
     * @return mixed
     * @datetime 2019/8/30 19:13
     * @author roach
     * @email jhq0113@163.com
     */
    static public function get($url,array $header = [])
    {
        return self::request('GET',$url,'',$header);
    }

    /**发送post请求
     * @param string        $url
     * @param array|string  $params
     * @param array         $header
     * @return mixed
     * @datetime 2019/8/30 19:15
     * @author roach
     * @email jhq0113@163.com
     */
    static public function post($url,$params = '',array $header = [])
    {
        return self::request('POST',$url,$params,$header);
    }

    /**发送put请求
     * @param string        $url
     * @param string        $params
     * @param array         $header
     * @return mixed
     * @datetime 2019/8/30 19:17
     * @author roach
     * @email jhq0113@163.com
     */
    static public function put($url,$params = '',array $header = [])
    {
        return self::request('PUT',$url,$params,$header);
    }

    /**发送delete请求
     * @param string        $url
     * @param string        $params
     * @param array         $header
     * @return mixed
     * @datetime 2019/8/30 19:17
     * @author roach
     * @email jhq0113@163.com
     */
    static public function delete($url,$params = '',array $header = [])
    {
        return self::request('DELETE',$url,$params,$header);
    }

    /**发送patch请求
     * @param string        $url
     * @param string        $params
     * @param array         $header
     * @return mixed
     * @datetime 2019/8/30 19:17
     * @author roach
     * @email jhq0113@163.com
     */
    static public function patch($url,$params = '',array $header = [])
    {
        return self::request('PATCH',$url,$params,$header);
    }

    /**发送header请求
     * @param string        $url
     * @param string        $params
     * @param array         $header
     * @return mixed
     * @datetime 2019/8/30 19:17
     * @author roach
     * @email jhq0113@163.com
     */
    static public function header($url,$params = '',array $header = [])
    {
        return self::request('HEADER',$url,$params,$header);
    }

    /**发送options请求
     * @param string        $url
     * @param string        $params
     * @param array         $header
     * @return mixed
     * @datetime 2019/8/30 19:17
     * @author roach
     * @email jhq0113@163.com
     */
    static public function options($url,$params = '',array $header = [])
    {
        return self::request('OPTIONS',$url,$params,$header);
    }
}