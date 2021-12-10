<?php

namespace Miqu\Core\Http;

use League\Route\RouteGroup;
use League\Route\Strategy\StrategyAwareInterface;
use League\Route\Strategy\StrategyInterface;

class Route
{
    /**
     * @param string $route
     * @param $callback
     * @return \League\Route\Route
     */
    public static function get(string $route, $callback): \League\Route\Route
    {
        return router()->get($route, $callback);
    }

    /**
     * @param string $route
     * @param $callback
     * @return \League\Route\Route
     */
    public static function post(string $route, $callback): \League\Route\Route
    {
        return router()->post($route, $callback);
    }

    /**
     * @param string $route
     * @param $callback
     * @return \League\Route\Route
     */
    public static function put(string $route, $callback): \League\Route\Route
    {
        return router()->put($route, $callback);
    }

    /**
     * @param string $route
     * @param $callback
     * @return \League\Route\Route
     */
    public static function delete(string $route, $callback): \League\Route\Route
    {
        return router()->delete($route, $callback);
    }

    /**
     * @param string $route
     * @param $callback
     * @return \League\Route\Route
     */
    public static function patch(string $route, $callback): \League\Route\Route
    {
        return router()->patch($route, $callback);
    }

    /**
     * @param string $route
     * @param $callback
     * @return \League\Route\Route
     */
    public static function options(string $route, $callback): \League\Route\Route
    {
        return router()->options($route, $callback);
    }

    /**
     * @param string $route
     * @param $callback
     * @return \League\Route\Route
     */
    public static function head(string $route, $callback): \League\Route\Route
    {
        return router()->head($route, $callback);
    }

    /**
     * @param string $method
     * @param string $route
     * @param $callback
     * @return \League\Route\Route
     */
    public static function map(string $method, string $route, $callback): \League\Route\Route
    {
        return router()->map($method, $route, $callback);
    }

    /**
     * @param string $prefix
     * @param $callback
     * @return RouteGroup
     */
    public static function group(string $prefix, $callback): RouteGroup
    {
        return router()->group($prefix, $callback);
    }

    /**
     * @param string $route
     * @param string $controller
     * @return RouteGroup
     */
    public static function resource(string $route, string $controller): RouteGroup
    {
        return self::group($route, function (RouteGroup $group) use ($controller) {
            $group->get('/', [ $controller, 'index' ]);
            $group->get('/{id}', [ $controller, 'show' ]);
            $group->get('/create', [ $controller, 'create' ]);
            $group->post('/create', [ $controller, 'store' ]);
            $group->get('/edit/{id}', [ $controller, 'edit' ]);
            $group->post('/edit/{id}', [ $controller, 'update' ]);
            $group->post('/delete/{id}', [ $controller, 'destroy' ]);
        });
    }

    /**
     * @param string $route
     * @param string $controller
     * @return RouteGroup
     */
    public static function apiResource(string $route, string $controller): RouteGroup
    {
        return self::group($route, function (RouteGroup $group) use ($controller) {
            $group->get('/', [ $controller, 'index' ]);
            $group->get('/{id}', [ $controller, 'show' ]);
            $group->put('/create', [ $controller, 'store' ]);
            $group->patch('/edit/{id}', [ $controller, 'update' ]);
            $group->delete('/delete/{id}', [ $controller, 'destroy' ]);
        });
    }

    /**
     * @param StrategyInterface $strategy
     * @return StrategyAwareInterface
     */
    public static function setStrategy(StrategyInterface $strategy): StrategyAwareInterface
    {
        return router()->setStrategy($strategy);
    }
}