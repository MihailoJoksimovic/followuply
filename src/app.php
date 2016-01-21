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

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'login' => array(
            'pattern' => '^/login$',
        ),
        'dashboard' => array(
            'pattern' => '^/dashboard',
            'form' => array('login_path' => '/login', 'check_path' => '/dashboard/login_check'),
            'logout' => array('logout_path' => '/dashboard/logout', 'invalidate_session' => true),
            'users' => function() use ($app) {
                return $app['service.user_provider'];
             },
        ),
    ),
    'security.role_hierarchy' => array(
        'ROLE_ADMIN' => array('ROLE_USER', 'ROLE_ALLOWED_TO_SWITCH'),
    )
));

if (file_exists(__DIR__.'/../config/db_config_local.php')) {
    $db_options = require_once __DIR__.'/../config/db_config_local.php';
} else {
    $db_options = array(
        'driver'    => 'pdo_mysql',
        'host'      => '127.0.0.1',
        'dbname'    => 'followuply',
        'user'      => 'root',
        'password'  => 'root',
        'charset'   => 'utf8mb4',
    );
}

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => $db_options
));

$app->register(new \Silex\Provider\CsrfServiceProvider());
$app->register(new \Silex\Provider\FormServiceProvider());
$app->register(new \Silex\Provider\TranslationServiceProvider(), array(
    'locale'    => 'en',
    'locale_fallbacks' => array('en'),
));

$app['db.orm.em'] = function ($app){
    $config = Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(array(__DIR__."/Followuply"), $app['debug']);

    $entityManager = \Doctrine\ORM\EntityManager::create($app['db.options'], $config);

    return $entityManager;
};

/** Shorthand for Entitymanager :) */
$app['em'] = function($app) {
    return $app['db.orm.em'];
};

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

$app['redis.client'] = function($c) {
    $redis = new Redis();
    $redis->connect($c['redis.host'], $c['redis.port']);

    return $redis;
};

$app['orm.repository.user'] = function($app) {
    $em = $app['em'];

    $repository = $em->getRepository('Followuply\Entity\User');

    return $repository;
};

$app['service.user_provider'] = function($app) {
    $em = $app['em'];

    $userProvider = new Followuply\Security\UserProvider($em);

    return $userProvider;
};

$app['service.user_registration_service'] = function($app) {
    $userRepository = $app['orm.repository.user'];
    $logger = $app['logger'];
    $encoder = $app['security.encoder_factory'];

    $service = new \Followuply\Security\UserRegistrationService($userRepository, $encoder, $logger);

    return $service;
};

return $app;
