<?php

use SCT\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HomeController
 * @package Controllers
 */
class HomeController extends Controller
{
    public function indexAction(Request $request)
    {
        var_dump($request);

        return new Response();
    }

    public function exampleAction(string $name)
    {
        $name = ucwords($name);

        $response = new Response("Hello there $name!");

        return $response;
    }
}