<?php

namespace Miqu\Core\Http\Strategies;

use League\Route\Route;
use League\Route\Strategy\ApplicationStrategy;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class InjectorStrategy extends ApplicationStrategy
{
    public function getContainer(): ?ContainerInterface
    {
        return app()->container;
    }

    public function invokeRouteCallable(Route $route, ServerRequestInterface $request): ResponseInterface
    {
        global $container;
        $this->setContainer($container);

        $controller = $route->getCallable($container);

        if ( $controller instanceof \Closure )
            return call_user_func_array( $controller, [ $request ] );

        return call_user_func_array( [ $controller[0], $controller[1] ], [ $request ] );
    }
}