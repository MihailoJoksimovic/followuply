<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Followuply\Model\Event;

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
    $event  = Event::fromRequest($request);

    $app['redis.client']->lPush(Event::REDIS_KEY, json_encode($event));

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

    $user = new Followuply\Entity\User();

    // find the encoder for a UserInterface instance
    $encoder = $app['security.encoder_factory']->getEncoder($user);

// compute the encoded password for foo
    $password = $encoder->encodePassword('foo', $user->getSalt());
var_dump($password);die();
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

$app->get('/dashboard', function() use ($app) {
    return "Welcome to Dashboard!";
})->bind('dashboard');

$app->get('/dashboard/login_check', function() use ($app) {
    return "Welcome to Admin!";
});

$app->get('/login', function(Request $request) use ($app) {
    return $app['twig']->render('login.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
});

$app->match('/register', function(Request $request) use ($app) {
    $user = new \Followuply\Entity\User();
    $user->setEmail('aaa');

    /** @var $form Symfony\Component\Form\Form */
    $form = $app['form.factory']->createBuilder('form', $user)
        ->add('email', 'email')
        ->add('password', 'password')
        ->add('save', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array('label' => 'Register'))
        ->getForm();

    $form->handleRequest($request);

    if (!$form->isSubmitted() || !$form->isValid()) {
        // display the form
        return $app['twig']->render('register.twig', array('form' => $form->createView()));
    }

    /** @var $service \Followuply\Security\UserRegistrationServiceInterface */
    $service = $app['service.user_registration_service'];

    try {
        $service->register($user);

        // TODO: Log In user after successful registration!

        return $app->redirect('dashboard');
    } catch (\Followuply\Security\UserAlreadyExistsException $e) {
        $form->addError(new \Symfony\Component\Form\FormError('Email already exists'));

        // display the form
        return $app['twig']->render('register.twig', array('form' => $form->createView()));
    }

})->bind('register');

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
