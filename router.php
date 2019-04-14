<?php declare(strict_types=1);

/**
 * This file is needed for php's builtin dev web server
 */
require_once __DIR__ . '/src/bootstrap.php';

$parsed = parse_url($_SERVER["REQUEST_URI"]);
$path = $parsed['path'] ?? '';

switch($path) {
    case '/tools/shorten':
        include __DIR__ . '/shorten.php';
        break;
    case '/tools/stats':
        include __DIR__ . '/stats.php';
        break;
    default:
        include __DIR__ . '/index.php';
        break;
}
