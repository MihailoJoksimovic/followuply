<?php


require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Console\SendEmailsCommand;

$application = new Application();
$application->add(new SendEmailsCommand());
$application->run();