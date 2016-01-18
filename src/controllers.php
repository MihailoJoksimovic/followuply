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

$app->post("/api/event/submit", function(Request $request) use ($app) {
    // I'll assume that data is valid

    $key = 'events';

    $event = [
        'uri'           => $request->get('uri'),
        'timestamp'     => $request->get('timestamp'),
        'visitor_uid'   => $request->get('visitor_uid'),
        'app_id'        => $request->get('app_id')
    ];

    /** @var $redis Redis */
    $redis = $app['redis.client'];

    $redis->lPush($key, json_encode($event));

    return new JsonResponse(array(
        'success' => true
    ));
});

$app->post('/api/pageview/submit', function(Request $request) use ($app) {
    $url    = urldecode($request->get('url'));
    $email  = urldecode($request->get('email',''));
    $uid    = $request->get('uid');

    /** @var $em \Doctrine\ORM\EntityManagerInterface */
    $em = $app['db.orm.em'];

    $pageview = new Followuply\Entity\PageView();
    $pageview->setEmail($email);
    $pageview->setUrl($url);
    $pageview->setVisitorUid($uid);

    $em->persist($pageview);

    $em->flush();

    return $app->json(array(
        'success'   => true,
        'message'   => 'Thanks!'
    ));
});

$app->get('/test/mixa', function() use ($app) {
    /** @var $em \Doctrine\ORM\EntityManager */
    $em = $app['em'];

//    $scenario = new Followuply\Entity\Scenario();
//    $scenario->setAppUid(12345);
//    $scenario->setTimeframe(10);
//    $em->persist($scenario);

    /** @var $scenario \Followuply\Entity\Scenario */
//    $scenario = $em->getRepository('Followuply\Entity\Scenario')->find(5);
//    var_dump($scenario->getRoutes()->first()->getScenario());die();

//    $route = new \Followuply\Entity\Route();
//    $route->setPatternType(\Followuply\Entity\Route::ROUTE_TYPE_BEGINS_WITH);
//    $route->setUriPattern('/upgrade');
//    $route->setPosition(1);
//    $route->setScenario($scenario);
//    $em->persist($route);
//    $em->flush();

    /** @var $route \Followuply\Entity\Route */
    $route = $em->getRepository('Followuply\Entity\Route')->find(8);

    var_dump($route->getScenario()->getAppUid());die();

//    $em->flush();

//    $route = new Followuply\Entity\Route();

});

$app->get('/test/a', function() use ($app) {
    return $app['twig']->render('test-a.twig');
});

$app->get('/test/b', function() use ($app) {
    return $app['twig']->render('test-b.twig');
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
