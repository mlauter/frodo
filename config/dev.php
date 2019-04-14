<?php declare(strict_types=1);

/**
 * Development server config
 */
$config = [
    'logger' => [
        'path' => 'php://stderr',
        'level' => Monolog\Logger::DEBUG,
    ],
    'db' => [
        'file' => dirname(__DIR__) . '/data/frodo.db',
    ]
];

$GLOBALS['server_config'] = $config;
