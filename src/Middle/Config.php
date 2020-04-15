<?php

namespace Ylyphp\Middle;

class Config
{
    // 中台网关地址
    static $baseURL = null;

    // 授权
    static $authorization = null;

    // 来源id (server_id)
    static $fromId = 0;
    static $clientId;
    static $clientSecret;

    const CREATE = 'create';
    const UPDATE = 'update';

    const CLIENT_HTTP = 'http';
    const CLIENT_SOCKET = 'socket';


    /**
     * 初始化配置文件
     *
     * @param  array  $options
     * @throws \Exception
     */
    public static function init($options = [])
    {
        if (!isset($options['client_id'])) {
            throw new \Exception('middle-platform: client_id is empty');
        }

        if (!isset($options['client_secret'])) {
            throw new \Exception('middle-platform: client_secret is empty');
        }

        // base_url 可以为null，因为可以使用绝对路径
        self::$baseURL = isset($options['base_url']) ? $options['base_url'] : 'http://ylygw.internal.101mama.cn';
        self::$fromId = $options['client_id'];
        self::$clientId = $options['client_id'];
        self::$clientSecret = $options['client_secret'];
    }
}
