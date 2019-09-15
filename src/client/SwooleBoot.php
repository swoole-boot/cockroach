<?php
namespace cockroach\client;

use cockroach\base\Container;
use cockroach\extensions\EHttp;
use cockroach\extensions\EString;

/**
 * Class SwooleBoot
 * @package cockroach\client
 * @datetime 2019/9/13 16:30
 * @author roach
 * @email jhq0113@163.com
 */
class SwooleBoot extends Socket
{
    /**
     * @var \cockroach\packages\SwooleBoot
     * @datetime 2019/9/13 16:31
     * @author roach
     * @email jhq0113@163.com
     */
    public $packager = [
        'class' => 'cockroach\packages\SwooleBoot'
    ];

    /**
     * @param array $config
     * @datetime 2019/9/13 17:31
     * @author roach
     * @email jhq0113@163.com
     */
    public function init($config = [])
    {
        parent::init($config);
        if(!isset($config['packager'])) {
            $this->packager = Container::insure($this->packager);
        }
    }

    /**
     * @param string $func
     * @param array $params
     * @return array|mixed|string|void
     * @datetime 2019/9/13 17:20
     * @author roach
     * @email jhq0113@163.com
     */
    public function call($func,$params)
    {
        $params['requestId'] = isset($params['requestId']) ? $params['requestId'] : EString::requestId('boot');
        $params['clientIp']  = isset($params['clientIp']) ? $params['clientIp'] : EHttp::getClientIp();

        return $this->request([
            'func'   => $func,
            'params' => $params
        ]);
    }
}