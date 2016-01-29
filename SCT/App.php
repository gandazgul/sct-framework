<?php namespace SCT;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class App
 *
 */
class App
{
    private $routes;
    private $response;

    public function __construct()
    {
        $this->routes = new RouteCollection();
        $this->response = new Response();
    }

    /**
     * Adds a route
     *
     * @param string|array $methods Either 'GET' or ['GET', 'POST']
     * @param string $url the url to match
     * @param string $controller_spec in the form ControllerClass::method
     * @param array $settings array with route settings
     *          e.g. for: '/user/{name}' $settings = [
     *              'name': [
     *                  'requirement': '[a-z]+',
     *                  'default': 'kevin'
     *              ]
     *          ]
     */
    public function addRoute($methods, string $url, string $controller_spec, array $settings = [])
    {
        $defaults = ['_controller' => $controller_spec];
        $requirements = [];

        foreach ($settings as $key => $setting)
        {
            $defaults[$key] = $setting['default'];
            $requirements[$key] = $setting['requirement'];
        }

        if (is_string($methods))
        {
            $methods = [$methods];
        }

        $route = new Route($url, $defaults, $requirements, [], '', [], $methods);

        $this->routes->add($url, $route);
    }

    /**
     * Ads a GET route
     * @see addRoute
     *
     * @param string $url
     * @param string $controller_spec
     * @param array $settings
     */
    public function get(string $url, string $controller_spec, array $settings = [])
    {
        $this->addRoute('GET', $url, $controller_spec, $settings);
    }

    /**
     * Ads a POST route
     * @see addRoute
     *
     * @param string $url
     * @param string $controller_spec
     * @param array $settings
     */
    public function post(string $url, string $controller_spec, array $settings = [])
    {
        $this->addRoute('POST', $url, $controller_spec, $settings);
    }

    /**
     * Adds a PUT route
     * @see addRoute
     *
     * @param string $url
     * @param string $controller_spec
     * @param array $settings
     */
    public function put(string $url, string $controller_spec, array $settings = [])
    {
        $this->addRoute('PUT', $url, $controller_spec, $settings);
    }

    /**
     * Adds a DELETE route
     * @see addRoute
     *
     * @param string $url
     * @param string $controller_spec
     * @param array $settings
     */
    public function delete(string $url, string $controller_spec, array $settings = [])
    {
        $this->addRoute('DELETE', $url, $controller_spec, $settings);
    }

    public function dispatch(Request $request)
    {
        $context = new RequestContext();
        $context->fromRequest($request);

        $matcher = new UrlMatcher($this->routes, $context);
        $request_stack = new RequestStack();

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new RouterListener($matcher, $request_stack, $context)); // IMPLEMENT: logger?

        $resolver = new ControllerResolver(); //IMPLEMENT logger?

        $kernel = new HttpKernel($dispatcher, $resolver);

        $kernel->handle($request)->send();
    }
}