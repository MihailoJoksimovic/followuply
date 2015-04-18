<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

//Request::setTrustedProxies(array('127.0.0.1'));

ErrorHandler::register();
ExceptionHandler::register($app['debug']);

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.twig', array());
})
->bind('homepage')
;

$app->post('/api/event/submit', function(Request $request) use ($app) {
    $url    = $request->get('url');
    $params = $request->get('params');

    $insertData = array(
        'url'   => $url,
        'email' => $params['email']
    );

    /** @var $mongoCollection MongoCollection */
    $mongoCollection = $app['mongo.collection.web_events'];
    $mongoCollection->insert($insertData);

    return $app->json(array(
        'success'   => true,
        'message'   => 'Thanks!'
    ));
});

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.twig',
        'errors/'.substr($code, 0, 2).'x.twig',
        'errors/'.substr($code, 0, 1).'xx.twig',
        'errors/default.twig',
    );

    $app['monolog.uncaught_errors']->info($e);

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
