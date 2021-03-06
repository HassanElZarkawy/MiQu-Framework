<?php

namespace Miqu\Core;

use Miqu\Core\Http\HeadersBag;
use Miqu\Core\Http\HttpRequest;
use Laminas\Diactoros\Uri;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\MethodNotAllowedException;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Http\Exception\UnauthorizedException;
use League\Route\Router;
use League\Uri\Http;
use Miqu\Core\Http\Strategies\LightWeightStrategy;
use Miqu\Core\Http\Strategies\StrategyBase;
use ReflectionException;
use RuntimeException;
use function Laminas\Diactoros\normalizeUploadedFiles;

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

    /**
     * @throws ReflectionException
     */
    public function __construct()
    {
        global $container;
        $this->container = $container;

        $this->request = $this->getServerRequest();
        $this->uri = Http::createFromServer($_SERVER);

        $this->router = (new Router);
        $this->setApplicationStrategy();
        $this->setDefaultMiddlewares();

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
        $this->bootServiceProviders();
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

    /**
     * @throws ReflectionException
     */
    public function setApplicationStrategy(): void
    {
        $strategy = \Miqu\Helpers\env('http.strategy');
        if ( $strategy === null )
            $strategy = LightWeightStrategy::class;

        $instance = $this->make($strategy);

        if ( ! ( $instance instanceof StrategyBase ) )
            throw new RuntimeException(
                sprintf( 'Strategy %s must implement %s class', $instance, StrategyBase::class )
            );

        $this->router->setStrategy($instance);
    }

    /**
     * @throws ReflectionException
     */
    private function setDefaultMiddlewares(): void
    {
        if (\Miqu\Helpers\env('http.middlewares'))
            foreach (\Miqu\Helpers\env('http.middlewares') as $middleware)
                $this->router->middleware($this->container->Resolve($middleware));
    }

    /**
     * @return HttpRequest
     */
    private function getServerRequest() : HttpRequest
    {
        $request_body = $this->parseRequestBody();
        return new HttpRequest(
            $_SERVER, normalizeUploadedFiles($_FILES),
            new Uri($_SERVER['REQUEST_URI']),
            $_SERVER['REQUEST_METHOD'], 'php://input', getallheaders(),
            $_COOKIE, $_GET, $request_body, $_SERVER['SERVER_PROTOCOL']
        );
    }

    /**
     * @return array|null
     */
    private function parseRequestBody() : ?array
    {
        $method = strtolower( $_SERVER['REQUEST_METHOD'] );
        if ( ! in_array( $method, [ 'post', 'put', 'patch', 'delete' ] ) )
            return null;

        return $_POST;
    }
}
