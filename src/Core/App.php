<?php

namespace Miqu\Core;

use Miqu\Core\Http\HeadersBag;
use Miqu\Core\Http\HttpRequest;
use Miqu\Core\Http\Strategies\InjectorStrategy;
use Laminas\Diactoros\Uri;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\MethodNotAllowedException;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Http\Exception\UnauthorizedException;
use League\Route\Router;
use League\Uri\Http;
use ReflectionException;

class App
{
    use BufferAwareTrait, InvokesServiceProviders;

    /**
     * @var Container 
     */
    public $container;

    /**
     * @var HttpRequest
     */
    private $request;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Http
     */
    private $uri;

    public function __construct()
    {
        global $container;
        $this->container = $container;

        $this->request = $this->getServerRequest();
        $this->uri = Http::createFromServer($_SERVER);

        $this->router = (new Router)->setStrategy(new InjectorStrategy);

        if ( strtolower( $this->request->getMethod() ) === 'post' )
            session( 'old', serialize( $this->request->getParsedBody() ) );

        $this->registerProviders();
    }

    /**
     * Starts the app and invokes the controller
     * @return void
     * @throws ReflectionException
     */
    public function start() : void
    {
        try {
            $response = $this->router->dispatch($this->request);
        } catch(NotFoundException $notFoundException) {
            $response = response()->notFound();
        } catch(UnauthorizedException $unauthorizedException) {
            $response = response()->unauthorized();
        } catch(BadRequestException $badRequestException) {
            $response = response()->badRequest();
        } catch(MethodNotAllowedException $methodNotAllowedException) {
            $response = response()->notAcceptable();
        }

        if ( $this->outputBufferStarted() )
            $this->clearOutputBuffer();

        $response = HeadersBag::addToResponse($response);
        CookiesBag::addToResponse($response);

        unset( $_SESSION[ 'old' ] );

        (new SapiEmitter)->emit($response);
        exit;
    }

    /**
     * Makes an instance of a class
     * @param $abstract string a fully qualified name of a class eg ( Miqu\Core\Http\Request::class )
     * @return mixed an instance made from $abstract with all of it's dependencies
     * @throws ReflectionException
     */
    public function make( string $abstract )
    {
        return $this->container->Resolve( $abstract );
    }

    /**
     * @return Router
     */
    public function router(): Router
    {
        return $this->router;
    }

    /**
    * Returns an instance of Request class
    * @return HttpRequest
    */
    public function request() : HttpRequest
    {
        return $this->request;
    }

    /**
     * @return Http
     */
    public function uri(): Http
    {
        return $this->uri;
    }

    private function getServerRequest() : HttpRequest
    {
        $request_body = $this->parseRequestBody();
        $this->request = new HttpRequest(
            $_SERVER, $_FILES, new Uri($_SERVER['REQUEST_URI']),
            $_SERVER['REQUEST_METHOD'], 'php://input', getallheaders(),
            $_COOKIE, $_GET, $request_body, $_SERVER['SERVER_PROTOCOL']
        );

        return $this->request;
    }

    private function parseRequestBody() : ?array
    {
        if ( ! ( strtolower( $_SERVER['REQUEST_METHOD'] ) === 'post' ) )
            return null;

        return $_POST;
    }
}
