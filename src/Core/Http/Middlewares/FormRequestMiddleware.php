<?php

namespace Miqu\Core\Http\Middlewares;

use Closure;
use Laminas\Diactoros\Uri;
use League\Route\Dispatcher;
use League\Route\Route;
use Miqu\Core\Http\FormRequest;
use Miqu\Core\Http\Strategies\ApiStrategy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use function Laminas\Diactoros\normalizeUploadedFiles;

class FormRequestMiddleware implements MiddlewareInterface
{
    /**
     * @throws ReflectionException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
       if ($handler instanceof Dispatcher)
       {
           $uri        = $request->getUri()->getPath();
           $httpMethod = $request->getMethod();
           $routeMap   = $this->getRoutesMap($handler);
           if (isset($routeMap[$httpMethod][$uri]))
           {
               /** @var Route $route */
               $route = $routeMap[$httpMethod][$uri];
               $callable = $route->getCallable(app()->container);
               $formRequests = $this->getFormRequestInstance(
                   is_array($callable) ? $callable[0] : $callable,
                   is_array($callable) ? $callable[1] : null
               );

               $issues = collect($formRequests)->map(function(FormRequest $request) {
                   $validation = validate($request->rules());
                   if ($validation->passes())
                       return null;
                   $errors = collect($validation->errors()->toArray())->map(function($message, $key) {
                       return collect($message)->first();
                   })->all();
                   return [
                       'errors' => $errors,
                       'redirect' => $request->getRedirectUri()
                   ];
               })->filter(function($items) {
                   return $items !== null;
               });

               if ( $issues->count() === 0 )
                   return $handler->handle($request);

               $strategy = $handler->getStrategy();
               if ($strategy instanceof ApiStrategy)
               {
                   $errors = $issues->map(function($err) {
                       return $err['errors'];
                   });
                   return response()->json([
                       'message' => current($errors->first()),
                       'errors' => $errors->all(),
                       'status' => 406
                   ])->withStatus(406, current($errors->first()));
               }
               else
               {
                   $redirectUri = $issues->first()['redirect'];
                   if ($redirectUri)
                       return response()->redirect($redirectUri, 406);
                   else
                       return response()->notAcceptable();
               }
           }
       }
        return $handler->handle($request);
    }

    private function getRoutesMap(Dispatcher $dispatcher)
    {
        $reflection = new ReflectionClass($dispatcher);
        $property = $reflection->getProperty('staticRouteMap');
        $property->setAccessible(true);
        return $property->getValue($dispatcher);
    }

    /**
     * @param mixed $object
     * @param string|null $function
     * @return array
     * @throws ReflectionException
     */
    private function getFormRequestInstance($object, string $function = null): array
    {
        if ($object instanceof Closure)
        {
            $reflection = new ReflectionFunction($object);
            $parameters = $reflection->getParameters();
        }
        else
        {
            $reflection = new ReflectionClass($object);
            $functionReflection = $reflection->getMethod($function);
            $parameters = $functionReflection->getParameters();
        }
        $instances = [];
        foreach ($parameters as $parameter)
        {
            $paramType = $parameter->getType();
            if (is_subclass_of($paramType->getName(), FormRequest::class))
                $instances[] = $this->createFormRequest($paramType->getName());
        }
        return $instances;
    }

    private function createFormRequest($class)
    {
        $requestMethod = strtolower( $_SERVER['REQUEST_METHOD'] );
        $parsedBody = in_array( $requestMethod, [ 'post', 'put', 'patch', 'delete' ] ) ? $_POST : null;
        return new $class(
            $_SERVER, normalizeUploadedFiles($_FILES),
            new Uri($_SERVER['REQUEST_URI']),
            $_SERVER['REQUEST_METHOD'], 'php://input', getallheaders(),
            $_COOKIE, $_GET, $parsedBody, $_SERVER['SERVER_PROTOCOL']
        );
    }
}