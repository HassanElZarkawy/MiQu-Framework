<?php

namespace Miqu\Core\Security\Middlewares;

use Exception;
use Miqu\Core\Authentication;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Authenticate implements MiddlewareInterface
{
    /**
     * @var Authentication
     */
    private $authenticationManager;

    /**
     * AuthenticateAdmin constructor.
     * @param Authentication $authenticationManager
     */
    public function __construct(Authentication $authenticationManager)
    {
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if( ! $this->authenticationManager->check() )
            return response()->unauthorized();

        return $handler->handle($request);
    }
}