<?php

// configure your app for the production environment

if (
    (isset($_SERVER['HTTP_HOST']) && stripos($_SERVER['HTTP_HOST'], 'followuply.dev') !== false)
    || getenv('ENABLE_DEBUG') == 1
) {
    $app['debug'] = true;
}

$app['twig.path'] = array(__DIR__.'/../templates');
$app['twig.options'] = array('cache' => __DIR__.'/../var/cache/twig');

// Mongo Settings
$app['mongo.host']      = '127.0.0.1';
$app['mongo.db_name']   = 'followuply';

if (file_exists(__DIR__.'/db_config_local.php')) {
    $app['db.options'] = require_once __DIR__.'/db_config_local.php';
} else {
    $app['db.options'] = array(
        'driver'    => 'pdo_mysql',
        'host'      => '127.0.0.1',
        'dbname'    => 'followuply',
        'user'      => 'root',
        'password'  => 'root',
        'charset'   => 'utf8mb4',
    );
}


$app['asset_path'] = 'http://followuply.dev';
