<?php

// configure your app for the production environment

if (isset($_SERVER['HTTP_HOST']) && stripos($_SERVER['HTTP_HOST'], 'followuply.dev') !== false) {
    $app['debug'] = true;
}

$app['twig.path'] = array(__DIR__.'/../templates');
$app['twig.options'] = array('cache' => __DIR__.'/../var/cache/twig');

// Mongo Settings
$app['mongo.host']      = '127.0.0.1';
$app['mongo.db_name']   = 'followuply';

$app['db_config']  = array (
    'mysql_readwrite' => array(
        'driver'    => 'pdo_mysql',
        'host'      => 'mysql_read.someplace.tld',
        'dbname'    => 'my_database',
        'user'      => 'my_username',
        'password'  => 'my_password',
        'charset'   => 'utf8',
    ),
);

$app['asset_path'] = 'http://followuply.dev';
