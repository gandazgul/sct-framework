<?php

use SCT\App;
use Symfony\Component\HttpFoundation\Request;
use TestApplication\Routes;

define('ROOT', realpath('../../') . '/');
define('APPLICATION', realpath('..') . '/');
define('STORAGE', APPLICATION . 'storage/');

require_once ROOT . "vendor/autoload.php";
require_once ROOT . "settings.php";

$request = Request::createFromGlobals();

$app = new App();

Routes::init($app);

$app->dispatch($request);
