<?php

 /*
 | !!!! DO NOT EDIT THIS FILE !!!!
 |
 | You can change settings by setting them in the environment or .env
 | If there is something you need to change, but is not available as an environment setting,
 | request an environment variable to be created upstream or send a pull request.
 */

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single', 'flare'],
            'ignore_exceptions' => false,
        ],

        'console' => [
            'driver' => 'stack',
            'channels' => ['single', 'stdout', 'flare'],
            'ignore_exceptions' => false,
        ],

        'console_debug' => [
            'driver' => 'stack',
            'channels' => ['single', 'stdout_debug'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => env('APP_LOG', \LibreNMS\Config::get('log_file', base_path('logs/librenms.log'))),
            'formatter' => \App\Logging\NoColorFormatter::class,
            'level' => env('LOG_LEVEL', 'error'),
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => env('APP_LOG', \LibreNMS\Config::get('log_file', base_path('logs/librenms.log'))),
            'formatter' => \App\Logging\NoColorFormatter::class,
            'level' => env('LOG_LEVEL', 'error'),
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => \App\Logging\CliColorFormatter::class,
            'with' => [
                'stream' => 'php://stderr',
            ],
            'level' => 'debug',
        ],

        'stdout_debug' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => \App\Logging\CliColorFormatter::class,
            'with' => [
                'stream' => 'php://output',
            ],
            'level' => 'debug',
        ],

        'stdout' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => \App\Logging\CliColorFormatter::class,
            'with' => [
                'stream' => 'php://output',
            ],
            'level' => 'info',
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        'flare' => [
            'driver' => 'flare',
        ],
    ],

];
