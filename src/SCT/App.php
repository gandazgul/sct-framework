<?php namespace SCT;

use SCT\Exceptions\SCTException;
use SCT\Interfaces\TemplateEngineInterface;
use SCT\Settings\AppSettings;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class App
 *
 * This is the heart of the framework, gathers the routes and handles the requests
 */
class App
{
    /** @var Controller|callable */
    protected $controller;
    private $routes;

    public function __construct()
    {
        $this->routes = new RouteCollection();
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

    /**
     * This is a listener for KernelEvents::VIEW which will render a view or send JSON depending on the Accept header
     *
     * @param GetResponseForControllerResultEvent $event
     *
     * @throws SCTException
     * @throws \Exception
     */
    public function render(GetResponseForControllerResultEvent $event)
    {
        /** @var Response|array $result */
        $result = $event->getControllerResult();

        //if we have a response then just set that and return
        if ($result instanceof Response)
        {
            $event->setResponse($result);

            return;
        }

        //otherwise lets render a response
        $request = $event->getRequest();
        $response = new Response();

        $acceptableContentType = $request->getAcceptableContentTypes();

        if (in_array('text/html', $acceptableContentType))
        {
            $template = $this->controller->getTemplate();

            //if we don't have a template throw exception
            if (!$template)
            {
                throw new SCTException("Browser requested html but controller didn't specify a template.");
            }

            $defaultData = $this->controller->getDefaultData();
            if (is_array($result))
            {
                $defaultData = array_merge($defaultData, $result);
            }

            $response = $this->renderHTML($template, $defaultData);
        }
        else if (in_array('application/json', $acceptableContentType))
        {
            $response = new JsonResponse($result);
        }

        $event->setResponse($response);
    }

    /**
     * KernelEvents::CONTROLLER listener, used to get the controller instance
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $this->controller = $event->getController();

        if (is_array($this->controller))
        {
            $this->controller = $this->controller[0];
        }
    }

    /**
     * Main app dispatcher
     *
     * @param Request $request
     *
     * @throws \Exception
     */
    public function dispatch(Request $request)
    {
        $context = new RequestContext();
        $context->fromRequest($request);

        $matcher = new UrlMatcher($this->routes, $context);
        $request_stack = new RequestStack();

        $dispatcher = new EventDispatcher();
        //router
        $dispatcher->addSubscriber(new RouterListener($matcher, $request_stack, $context)); // IMPLEMENT: logger?
        //Get the controller
        $dispatcher->addListener(KernelEvents::CONTROLLER, [$this, 'onKernelController']);
        //non-response results
        $dispatcher->addListener(KernelEvents::VIEW, [$this, 'render']);

        $resolver = new ControllerResolver(); //IMPLEMENT lrogger?
        $kernel = new HttpKernel($dispatcher, $resolver);

        //create default response
        $response = new Response();

        try // to get a response from controller
        {
            $response = $kernel->handle($request);
        } catch (HttpException $e)
        {
            if ($e instanceof NotFoundHttpException)
            {
                //page not found
                $response = $this->getPageNotFoundResponse();
            }
        }

        $response->send();
    }

    /**
     * Crafts an HTML 404 Not found response
     */
    public function getPageNotFoundResponse()
    {
        $response = $this->renderHTML('errors/404.smarty');
        $response->setStatusCode(Response::HTTP_NOT_FOUND, "Page not found");

        return $response;
    }

    /**
     * Renders a template with the template engine, use $data to pass in variables that will be bound to the template
     *
     * @param string $template The name of the template to render
     * @param array|null $data Optional, empty by default
     *
     * @return Response
     */
    protected function renderHTML(string $template, $data = [])
    {
        $response = new Response();
        $response->headers->set('Content-type', 'text/html', true);

        $engineClassName = AppSettings::$view_engine;
        /** @var TemplateEngineInterface $engine */
        $engine = new $engineClassName(AppSettings::$view_folder);
        $response->setContent($engine->render($template, $data));

        return $response;
    }

    /**
     * @param Request $request The request object
     * @param string|null $where The destination url, if null will get it from the request
     * @param int $http_code The http code to use, 301 by default
     *
     * @return RedirectResponse
     */
    public function redirect(Request $request, $where = null, $http_code = 301)
    {
        if (!$where)
        {
            $where = $request->getRequestUri();
        }

        // location header must be a full URL
        if (strtolower(substr($where, 0, 4)) != 'http')
        {
            if (substr($where, 0, 2) !== '//')
            {
                $where = '//' . $request->getHost() . $where;
            }

            if ($request->getScheme() == 'https')
            {
                $where = "https:$where";
            }
            else
            {
                $where = "http:$where";
            }
        }

        $where_orig = $where; //filter_var will set the url to false if not valid. Keep the original.
        $where = filter_var($where, FILTER_VALIDATE_URL);
        if (!$where)
        {
            trigger_error("Destination url is not a valid url: $where_orig", E_USER_NOTICE);
            $response = new Response();
            $response->setStatusCode(500);

            return $response;
        }

        $response = new RedirectResponse($where, $http_code);

        return $response;
    }
}