<?php declare(strict_types=1);

// Report all errors by default
error_reporting(E_ALL | E_STRICT);

// Class autoloader
require_once __DIR__ . '/../vendor/autoload.php';

$config_map = [
    'production' => 'prod.php',
    'development' => 'dev.php',
    'testing' => 'test.php',
];

$env = $_SERVER['ENVIRONMENT'] ?? 'development';
if (!in_array($env, array_keys($config_map), true)) {
    $env = 'development';
}
$_SERVER['ENVIRONMENT'] = $env;

require_once __DIR__ . '/../config/' . $config_map[$env];

if (php_sapi_name() !== 'cli') {
    define('BASE_HREF', 'http://' . $_SERVER['HTTP_HOST'] . '/');
}

// TODO Setup monolog logging
$log_settings = $GLOBALS['server_config']['logger'];
$logger = new \Monolog\Logger('logger');
$logger->pushHandler(new \Monolog\Handler\StreamHandler($log_settings['path'], $log_settings['level']));
\Monolog\Registry::addLogger($logger);

// Setup error handlers
Frodo\ErrorHandler::init();
