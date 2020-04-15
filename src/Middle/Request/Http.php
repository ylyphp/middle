<?php

namespace Ylyphp\Middle\Request;

use Ylyphp\Middle\Config;
use Ylyphp\Middle\Log;
use GuzzleHttp\Exception\RequestException;

class Http extends BaseRequest implements RequestInterface
{

    public function init()
    {
        $this->getAuthorization();
    }

    /**
     * 获取header
     */
    protected function getAuthorization()
    {
        $authorization  = Config::$clientId . ':' .Config::$clientSecret;

        Config::$authorization = $authorization = 'Ylytk ' . base64_encode($authorization);

        return $authorization;
    }

    /**
     * 处理请求
     *
     * @return bool|string
     */
    public function request()
    {
        $parameters = func_get_args();

        list($url, $requestData) = func_get_args();
        count($parameters) == 3 && $method = $parameters[2];
        count($parameters) == 4 && $extra = $parameters[3];

        !isset($method) && $method = 'POST';
        !isset($extra) && $extra = [];

        strpos($url, 'http') === false && $url = Config::$baseURL . $url;

        $options = self::buildOptions($method, $requestData);

        $timeout = isset($other['timeout']) ? $extra['timeout'] : 5;
        $client = new \GuzzleHttp\Client(['verify' => false, \GuzzleHttp\RequestOptions::CONNECT_TIMEOUT => $timeout]);

        try {
            $response = $client->request($method, $url, $options);
            $statusCode = $response->getStatusCode();
            $content = $response->getBody()->getContents();

            Log::info('curlGuzzle, url='.$url.', options='.json_encode($options).', statusCode=' . $statusCode .', content='. $content);

            return json_decode($content, true);
        } catch (RequestException $e) {
            $content = $e->getResponse()->getBody()->getContents();
            Log::critical('curlGuzzle, url='.$url.', options='.json_encode($options).', error: '.$content);

            return json_decode($content, true);
        } catch (\Exception $e) {
            Log::error('curlGuzzle, url='.$url.', options='.json_encode($options).', error: '.$e->getMessage());
        }

        return false;
    }

    /**
     * build options
     *
     * @param $method
     * @param $requestData
     * @return array
     */
    private static function buildOptions($method, $requestData)
    {
        $options = [];

        $headers = [
            'Authorization' => Config::$authorization,
            'fromId' => Config::$fromId
        ];

        ($method === 'GET') && $options[\GuzzleHttp\RequestOptions::QUERY] = $requestData;

        ($method === 'POST') && $headers['Content-Type'] = 'application/json'
            && $options[\GuzzleHttp\RequestOptions::BODY] = json_encode($requestData, JSON_UNESCAPED_UNICODE);

        $options['headers'] = $headers;

        return $options;
    }
}