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

$app->get('/a', function () use ($app) {
    return $app['twig']->render('test-a.twig', array());
});

$app->get('/b', function () use ($app) {
    return $app['twig']->render('test-b.twig', array());
});

$app->post("/api/path/add", function(Request $request) use ($app) {
    $pageA           = $request->request->get('pageA');
    $pageB             = $request->request->get('pageB');
    $timeFrame       = $request->request->get('timeFrame');

    /** @var $em \Doctrine\ORM\EntityManagerInterface */
    $em = $app['db.orm.em'];

    $route = new Followuply\Entity\Route();
    $route->setPageA($pageA);
    $route->setPageB($pageB);
    $route->setTimeframe($timeFrame);

    /** @var $validator Symfony\Component\Validator\Validator\RecursiveValidator */
    $validator = $app['validator'];
    $errors = $validator->validate($route);

    if (count($errors) > 0) {
        return $app->json(array(
            'success' => false,
            'message' => 'An error has occurred. Please make sure you have filled out all the fields'
        ));
    }

    $em->persist($route);

    $em->flush();

    return $app->json(array(
        'success'   => true,
        'message'   => 'Path added successfully'
    ));
})->bind('api.path.add');

$app->get('/api/event/submit', function(Request $request) use ($app) {
    $url    = urldecode($request->get('url'));
    $cid    = $request->get('cid');
    $email  = urldecode($request->get('email',''));
    $params = $request->get('params', array());

    $insertData = array(
        'url'   => $url,
        'email' => $email,
        'params'    => $params,
        'client_id' => $cid,
        'dt_added'  => new MongoDate(),
        'processed' => false
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
