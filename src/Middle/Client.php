<?php

namespace Ylyphp\Middle;

use Exception;

/**
 * Class Client
 *
 * @method mixed request($url, $requestData, $method='', $timeout=5)
 */
class Client
{
    private $requestClass; // 服务名称
    private $options; // 配置

    /**
     * Client constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $params = func_get_args();
        if (count($params) === 0) {
            throw new Exception('middle-platform: please configure Class Client parameters');
        }

        if (count($params) === 1) {
            $serviceName = Config::CLIENT_HTTP;
            $options = $params[0];
        } else {
            list($options, $serviceName) = $params;
        }

        $this->options = $options;

        // 初始化 Log
        Log::init($options);

        // 初始化 Config
        $configClassName = __NAMESPACE__."\\".'Config';
        call_user_func_array([$configClassName, 'init'], [$options]);

        // 初始化request
        $requestClassName = __NAMESPACE__."\\".'Request'."\\".ucfirst($serviceName);
        if (!class_exists($requestClassName)) {
            throw new Exception('Request Class ['.$requestClassName.'] is not exist');
        }

        $this->requestClass = new $requestClassName();

        call_user_func_array([$this->requestClass, 'init'], []);
    }

    /**
     * 魔术方法
     *
     * @param  string  $method     动态请求方法
     * @param  array   $arguments  动态请求参数
     * @return mixed
     * @throws Exception
     */
    public function __call($method, $arguments)
    {
        if (!method_exists($this->requestClass, $method)) {
            throw new Exception('Request Class '.get_class($this->requestClass).' method ['.$method.'] is not exist');
        }

        return call_user_func_array([$this->requestClass, $method], $arguments);
    }

}
