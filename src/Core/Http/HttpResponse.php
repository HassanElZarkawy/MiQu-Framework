<?php

namespace Miqu\Core\Http;

use Miqu\Core\Interfaces\IView;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use ReflectionException;

class HttpResponse extends Response implements ResponseInterface
{
    private $container;

    public function __construct($body = 'php://memory', int $status = 200, array $headers = [])
    {
        parent::__construct($body, $status, $headers);
        global $container;
        $this->container = $container;
    }

    public function setCookie( string $cookie_name, string $cookie_value, int $expiresAt = 3600, string $path = '/', bool $secure = false, bool $http_only = false ) : void
    {
        $this->headers['Set-Cookie'] = [
            "$cookie_name=$cookie_value; path=$path; expires=$expiresAt" . ( $secure ? ' secure;' : '' ) . ( $http_only ? ' HttpOnly' : '' )
        ];
    }

    /**
     * @throws ReflectionException
     */
    public function view(string $name, $arguments = []) : self
    {
        /** @var IView $engine */
        $engine = $this->container->Resolve(IView::class);
        $engine->view($name);
        $content = $engine->with($arguments)->content();
        $this->getBody()->write($content);
        $this->headers['Content-Type'] = [
            'application/json', 'charset=utf-8'
        ];
        return $this;
    }

    public function json($object = null) : self
    {
        if ( is_null( $object ) )
            $object = [];
        $this->getBody()->write( json_encode( $object ) );
        $this->headers['Content-Type'] = [
            'application/json', 'charset=utf-8'
        ];
        return $this;
    }

    /**
     * @param string|null $route
     * @return $this
     */
    public function redirect(string $route, int $code = 302) : self
    {
        if ( ! string($route)->startsWith('http') )
        {
            if ( string($route)->startsWith('/') )
                $route = (string)string($route)->trimLeft('/');

            $route = (string)string($route)->prepend(getBaseUrl());
        }

        $this->headers['Location'] = [
            $route
        ];
        return $this->withStatus($code);
    }

    /**
     * @return HttpResponse
     * @throws ReflectionException
     */
    public function badRequest(): HttpResponse
    {
        return $this->view('errors.400')->withStatus(400);
    }

    /**
     * @return HttpResponse
     * @throws ReflectionException
     */
    public function unauthorized(): HttpResponse
    {
        return $this->view('errors.401')->withStatus(401);
    }

    /**
     * @return HttpResponse
     * @throws ReflectionException
     */
    public function forbidden() : HttpResponse
    {
        return $this->view('errors.403')->withStatus(403);
    }

    /**
     * @return HttpResponse
     * @throws ReflectionException
     */
    public function notFound(): HttpResponse
    {
        return $this->view('errors.404')->withStatus(404);
    }

    /**
     * @return HttpResponse
     * @throws ReflectionException
     */
    public function notAcceptable(): HttpResponse
    {
        return $this->view('errors.406')->withStatus(406);
    }

    /**
     * @return HttpResponse
     * @throws ReflectionException
     */
    public function error(): HttpResponse
    {
        return $this->view('errors.500')->withStatus(500);
    }
}