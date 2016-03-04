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
        $app->get('/', 'HomeController::index');
        $app->get('/hello/{name}', 'HomeController::example');

        //All other routes
    }
}