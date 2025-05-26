<?php

require_once __DIR__ . '/../vendor/autoload.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($uri) {
    case '/commits':
        require_once __DIR__ . '/commits.php';
        break;
    case '/':
    default:
        require_once __DIR__ . '/home.php';
        break;
} 