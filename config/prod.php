<?php declare(strict_types=1);

/**
 * Development server config
 */
$config = [
    'logger' => [
        'path' => '/var/log/apache2/frodo.log',
        'level' => Monolog\Logger::WARNING,
    ],
    'db' => [
        'file' => dirname(__DIR__) . '/data/frodo.db',
    ]
];

$GLOBALS['server_config'] = $config;
