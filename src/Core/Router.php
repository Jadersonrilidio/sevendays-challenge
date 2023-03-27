<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Core;

use Jayrods\ScubaPHP\Core\{Request, Response};
use Jayrods\ScubaPHP\Middleware\MiddlewareQueue;
use Jayrods\ScubaPHP\Utils\{Cached, FlashMessage};

class Router
{
    /**
     * 
     */
    private const REGEX_ROUTE_PARAM = '/\{([^\/]+?)\}/';

    /**
     * 
     */
    private const REGEX_URI_PARAM = '/\{.+?\}/';

    /**
     * 
     */
    private const REGEX_URI_PARAM_REPLACEMENT = '([^/]+?)';

    /**
     * 
     */
    private Request $request;

    /**
     * 
     */
    private array $routes;

    /**
     * 
     */
    private MiddlewareQueue $queue;

    /**
     * 
     */
    public function __construct(Request $request, array $routes)
    {
        $this->request = $request;
        $this->routes = $routes;

        $this->queue = new MiddlewareQueue();
    }

    /**
     * 
     */
    public function handleRequest(): Response
    {
        $routeParams = $this->routeParams();

        $controller = $routeParams[0];
        $method = $routeParams[1];
        $middlewares = $routeParams[2] ?? [];

        $this->executeMiddlewaresQueue($middlewares);

        $controller = new $controller(
            view: new View(),
            flashMsg: new FlashMessage()
        );

        return $controller->$method($this->request);
    }

    /**
     * 
     */
    private function routeParams()
    {
        $httpMethod = $this->request->httpMethod();
        $uri = $this->request->uri();

        $routeRegexArray = Cached::routeRegexArray() ?? $this->createRouteRegexArray($httpMethod);

        $requestRoute = "$httpMethod|$uri";

        foreach ($routeRegexArray as $route => $regex) {
            if (preg_match($regex, $requestRoute, $uriParamValues)) {
                if (preg_match_all(self::REGEX_ROUTE_PARAM, $route, $uriParamKeys)) {
                    unset($uriParamValues[0]);
                    $this->request->addUriParams($uriParamKeys[1], $uriParamValues);
                }

                return $this->routes[$route];
            }
        }

        return $this->routes['fallback'];
    }

    /**
     * 
     */
    private function createRouteRegexArray(): array
    {
        // Mount base regex array structure
        $regexArray = array_combine(array_keys($this->routes), array_keys($this->routes));

        // Replace URI params by regex group
        $regexArray = preg_replace(self::REGEX_URI_PARAM, self::REGEX_URI_PARAM_REPLACEMENT, $regexArray);

        // Format regex expression slashes
        $regexArray = str_replace('/', '\/', $regexArray);

        // Format regex expression slashes
        $regexArray = str_replace('|', '\|', $regexArray);

        // wrap regex expression with start and end signs
        $regexArray = array_map(function ($route) {
            return '/^' . $route . '$/';
        }, $regexArray);

        Cached::saveRouteRegexArray($regexArray);

        return $regexArray;
    }

    /**
     * 
     */
    private function executeMiddlewaresQueue(array $middlewares): bool
    {
        $this->queue->addMiddlewares($middlewares);

        return $this->queue->next($this->request);
    }

    /**
     * 
     */
    public static function redirect(string $path = ''): void
    {
        header("Location: " . SLASH . $path);
        exit;
    }
}