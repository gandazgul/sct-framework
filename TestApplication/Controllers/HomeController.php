<?php

use SCT\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HomeController
 *
 * @package Controllers
 */
class HomeController extends Controller
{
    /**
     * Example of returning and array and setting a view
     * Try calling this with both the browser and with AJAX
     *
     * @return array
     */
    public function index()
    {
        $this->template = 'welcome.phtml';

        return [
            'message' => 'This message came from the controller.',
        ];
    }

    /**
     * Example of using named params and returning a response
     *
     * @param string $name
     *
     * @return Response
     */
    public function example(string $name)
    {
        $name = ucwords($name);

        $response = new Response("Hello there $name!");

        return $response;
    }
}