<?php

namespace Miqu\Core;

use Monolog\Formatter\HtmlFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class LogManager
{
    /** @var Logger[]  */
    private static $loggers = [];

    public static function get(string $channel = 'default'): Logger
    {
        if ( isset( self::$loggers[ $channel ] ) )
            return self::$loggers[ $channel ];

        $logger = self::createLogger($channel);

        self::$loggers[$channel] = $logger;
        return $logger;
    }

    public static function remove(string $channel)
    {
        if ( array_key_exists( $channel, self::$loggers ) )
            unset( self::$loggers[ $channel ] );
    }

    /**
     * @param string $channel
     * @return Logger
     */
    private static function createLogger(string $channel): Logger
    {
        $log_file_path = (string)string(BASE_DIRECTORY)->append(DIRECTORY_SEPARATOR)->append(\Miqu\Helpers\env('logging.path'));

        $handler = new RotatingFileHandler($log_file_path, 100);
        $handler->setFormatter(new HtmlFormatter(\Miqu\Helpers\env('logging.time_format')));

        return (new Logger($channel))->pushHandler( $handler );
    }
}