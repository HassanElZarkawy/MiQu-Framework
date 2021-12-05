<?php

namespace Miqu\Core\Http\Strategies;

use Closure;
use Laminas\Diactoros\Uri;
use League\Route\Route;
use Miqu\Core\Http\HttpRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionParameter;
use function Laminas\Diactoros\normalizeUploadedFiles;

class InjectorStrategy extends StrategyBase
{
    /**
     * @param Route $route
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function invokeRouteCallable(Route $route, ServerRequestInterface $request): ResponseInterface
    {
        $controller = $route->getCallable($this->getContainer());
        $parameters = $this->getParameters(
            is_array($controller) ? $controller[0] : $controller,
            is_array($controller) ? $controller[1] : null
        );
        $instances = $this->getParametersInstances($parameters);

        if ( $controller instanceof Closure )
        {
            return call_user_func_array( $controller, $instances );
        }

        return call_user_func_array( [ $controller[0], $controller[1] ], $instances );
    }

    /**
     * @param $object
     * @param string|null $method
     * @return array
     * @throws ReflectionException
     */
    public function getParameters($object, string $method = null): array
    {
        if ($object instanceof Closure)
        {
            $funcReflection = new ReflectionFunction($object);
            $parameters = $funcReflection->getParameters();
        }
        else
        {
            $reflection = new ReflectionClass($object);
            $parameters = $reflection->getMethod($method)->getParameters();
        }
        return $parameters;
    }

    public function getParametersInstances(array $parameters): array
    {
        return collect($parameters)->map(function(ReflectionParameter $param) {
            $type = $param->getType()->getName();
            $defaultImplementations = $this->getDefaultMappings();
            foreach ( $defaultImplementations as $class => $factory )
            {
                if ($type === $class || is_subclass_of($type, $class) )
                    return $factory($type);
            }
            return app()->make($type);
        })->all();
    }

    private function getDefaultMappings(): array
    {
        return [
            HttpRequest::class => function(string $class) {
                $method = strtolower( $_SERVER['REQUEST_METHOD'] );
                $parsedBody = null;
                if ( in_array( $method, [ 'post', 'put', 'patch', 'delete' ] ) )
                    $parsedBody = $_POST;

                return new $class(
                    $_SERVER, normalizeUploadedFiles($_FILES),
                    new Uri($_SERVER['REQUEST_URI']),
                    $_SERVER['REQUEST_METHOD'], 'php://input', getallheaders(),
                    $_COOKIE, $_GET, $parsedBody, $_SERVER['SERVER_PROTOCOL']
                );
            },
        ];
    }
}