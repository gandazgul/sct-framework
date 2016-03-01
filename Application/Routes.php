<?php namespace Application;

use SCT\App;

/**
 * Class Routes
 *
 * @package Config
 */
class Routes
{
    public static function init(App $app)
    {
        $app->get('/', 'HomeController::indexAction');
        $app->get('/hello/{name}', 'HomeController::exampleAction');

        //All other routes
    }
}