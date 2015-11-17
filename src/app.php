<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\RoutingServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;

$app = new Application();
$app->register(new RoutingServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app['twig'] = $app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...

    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
        return $app['request_stack']->getMasterRequest()->getBasepath().'/'.$asset;
    }));

    return $twig;
});

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'    => 'pdo_mysql',
        'host'      => '127.0.0.1',
        'dbname'    => 'followuply',
        'user'      => 'followuply',
        'password'  => 'followuply',
        'charset'   => 'utf8',
    )
));

$app['monolog.uncaught_errors'] = function($c) use ($app) {
    /** @var $log \Monolog\Logger */
    $log = new \Monolog\Logger('uncaught_errors');

    $log->pushHandler(new \Monolog\Handler\StreamHandler(__DIR__.'/../var/logs/uncaught_errors.log', \Monolog\Logger::INFO));

    return $log;
};

$app['mongo.client']    = function($c) {
    $host = $c['mongo.host'];
    $client = new MongoClient($host);

    return $client;
};

$app['mongo.db']    = function($c) {
    $dbName = $c['mongo.db_name'];

    /** @var $client MongoClient */
    $client = $c['mongo.client'];

    return $client->$dbName;
};

$app['mongo.collection.web_events'] = function($c) {
    $collectionName = 'web_events';

    /** @var $db MongoDB */
    $db = $c['mongo.db'];

    $collection = $db->selectCollection($collectionName);

    $collection->ensureIndex(array('status' => 1));

    return $collection;
};


return $app;
