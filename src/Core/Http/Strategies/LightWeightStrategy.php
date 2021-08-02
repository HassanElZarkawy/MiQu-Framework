<?php

namespace Miqu\Core\Http\Strategies;

use Closure;
use League\Route\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class LightWeightStrategy extends StrategyBase
{
    public function invokeRouteCallable(Route $route, ServerRequestInterface $request): ResponseInterface
    {
        $callable = $route->getCallable();

        if (is_string($callable) && strpos($callable, '::') !== false) {
            $callable = explode('::', $callable);
        }

        if (is_array($callable) && isset($callable[0]) && is_object($callable[0])) {
            $callable = [$callable[0], $callable[1]];
        }

        if (!is_callable($callable)) {
            throw new RuntimeException('Could not resolve a callable for this route');
        }

        if ( $callable instanceof Closure )
            return $callable($request);

        $controller = new $callable[0];
        return $controller->{$callable[1]}($request);
    }
}