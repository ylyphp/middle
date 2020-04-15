<?php

namespace Ylyphp\Middle;

use Exception;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class Log
 *
 * @method static void debug(string $message, array $context = [])
 * @method static void info(string $message, array $context = [])
 * @method static void notice(string $message, array $context = [])
 * @method static void warning(string $message, array $context = [])
 * @method static void error(string $message, array $context = [])
 * @method static void critical(string $message, array $context = [])
 * @method static void alert(string $message, array $context = [])
 * @method static void emergency(string $message, array $context = [])
 */
class Log
{
    private static $logPath;
    private static $logInstance;

    /**
     * 初始化
     *
     * @param $options
     * @throws Exception
     */
    public static function init($options)
    {
        if (!isset($options['log_path'])) {
            throw new Exception('middle-platform: please configure log_path');
        }

        self::$logPath = $options['log_path'];
    }

    /**
     * 调用
     *
     * @param $name
     * @param $arguments
     * @throws \Exception
     */
    public static function __callStatic($name, $arguments)
    {
        if (!in_array(strtoupper($name), array_keys(Logger::getLevels()), true)) {
            throw new \Exception("Log method '".$name."' not exists");
        }

        if (false == (self::$logInstance instanceof Logger)) {
            // the default date format is "Y-m-d\TH:i:sP"
            $dateFormat = "Y-m-d\TH:i:sP";
            // the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
            $output = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
            // finally, create a formatter
            $formatter = new LineFormatter($output, $dateFormat);

            // Create a handler
            $stream = new StreamHandler(self::$logPath, Logger::DEBUG);
            $stream->setFormatter($formatter);
            // bind it to a logger object
            $logger = new Logger('MiddlePlatform');
            $logger->pushHandler($stream);
            self::$logInstance = $logger;
        } else {
            $logger = self::$logInstance;
        }

        call_user_func_array([$logger, strtolower($name)], $arguments);
    }
}
