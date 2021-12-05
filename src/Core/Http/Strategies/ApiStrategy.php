<?php

namespace Miqu\Core\Http\Strategies;

use League\Route\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

class ApiStrategy extends InjectorStrategy
{
    /**
     * @param Route $route
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function invokeRouteCallable(Route $route, ServerRequestInterface $request): ResponseInterface
    {
        $response = parent::invokeRouteCallable($route, $request);
        $response->getBody()->seek(0);
        $unparsedContents = json_decode($response->getBody()->getContents());
        $response->getBody()->rewind();
        $response->getBody()->write(json_encode([
            'data' => $unparsedContents,
            'status' => $response->getStatusCode()
        ]));
        return $response;
    }
}