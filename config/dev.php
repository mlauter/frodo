<?php declare(strict_types=1);

/**
 * Development server config
 */
$config = [
    'logger' => [
        'path' => 'php://stderr',
        'level' => Monolog\Logger::DEBUG,
    ],
];

$GLOBALS['server_config'] = $config;
