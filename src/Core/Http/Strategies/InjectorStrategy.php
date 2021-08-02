<?php

namespace Miqu\Core\Http\Strategies;

use League\Route\Route;
use League\Route\Strategy\ApplicationStrategy;
use League\Route\Strategy\JsonStrategy;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class InjectorStrategy extends StrategyBase
{
    public function invokeRouteCallable(Route $route, ServerRequestInterface $request): ResponseInterface
    {
        $controller = $route->getCallable($this->getContainer());

        if ( $controller instanceof \Closure )
            return call_user_func_array( $controller, [ $request ] );

        return call_user_func_array( [ $controller[0], $controller[1] ], [ $request ] );
    }
}