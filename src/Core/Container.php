<?php

namespace Miqu\Core;

use Miqu\Core\Interfaces\IContainer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use function in_array;

/**
* A light weight and simple dependency injection class
*/
class Container implements IContainer, ContainerInterface
{
    /**
    * Holds the class names and how to create them.
    */
    private $bindings = [];

    /**
     * @var array
     */
    private $singletons = [];

    /**
     * Registers a class for dependency injection.
     * @param $abstract string eg (MyService::class)
     * @param callable|string $factory callable function returns an instance of an $abstract
     * @return void
     */
    public function Register( string $abstract, $factory )
    {
        if ( is_string( $factory ) )
        {
            $factory = function($c) use ($factory) {
                return $c->Resolve($factory);
            };
        }
        $this->bindings[ $abstract ] = $factory;
    }

    /**
     * Registers a singleton class for dependency injection.
     * @param $abstract string eg (MyService::class)
     * @param callable|string $factory callable function returns an instance of an $abstract
     * @return void
     */
    public function RegisterSingleton( string $abstract, $factory )
    {
        if ( is_string( $factory ) )
        {
            $factory = function($c) use ($factory) {
                return $c->Resolve($factory);
            };
        }
        $this->singletons[ $abstract ] = $factory;
    }

    /**
     * Gets an instance of an abstract from the bindings.
     * If it doesn't exists, the function will try to make a new instance
     * @param string $abstract eg (MyService::class)
     * @return mixed
     * @throws ReflectionException
     */
    public function Resolve( string $abstract )
    {
        $abstract = str_replace( '/', '\\', $abstract );

        if ( isset( $this->singletons[ $abstract ] ) )
        {
            if ( is_callable( $this->singletons[ $abstract ] ) )
                $this->singletons[ $abstract ] = call_user_func_array( $this->singletons[ $abstract ], [ $this ] );

            return $this->singletons[ $abstract ];
        }

        if ( isset( $this->bindings[ $abstract ] ) )
            return call_user_func_array( $this->bindings[ $abstract ], [ $this ] );

        $reflection = new ReflectionClass( $abstract );
        $dependencies = $this->buildDependencies( $reflection );
        return $reflection->newInstanceArgs( $dependencies );
    }

    /**
     * Tries to create a new instance of a class based on it's dependencies.
     * @param $reflection /ReflectionClass eg (Controllers\HomeController::class)
     * @return array
     * @throws ReflectionException
     */
    private function buildDependencies(ReflectionClass $reflection ): array
    {
        if ( ! $constructor = $reflection->getConstructor() ) 
            return [];

        $params = $constructor->getParameters();

        return array_map( function ( $param ) use ( $reflection ) 
        {
            /** @var ReflectionClass $type */
            $type = $param->getType();
            if ( ! $type )
            {
                if ( $param->isDefaultValueAvailable() )
                    return $param->getDefaultValue();

                if ( $param->isOptional() || $param->isArray() )
                    return [];

                $message = 'Unable to get $' . $param . ' on' . $reflection->getName();
                
                throw new RuntimeException( $message );
            }

            $shippable_types = [ 'int', 'float', 'bool', 'string', 'null', 'array' ];
            if ( in_array( $type, $shippable_types ) )
            {
                if ( $param->isDefaultValueAvailable() )
                    return $param->getDefaultValue();

                if ( $param->isOptional() || $param->isArray() )
                    return [];
            }

            return $this->Resolve( $type->getName() );
        }, $params );
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return mixed Entry.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ReflectionException
     */
    public function get(string $id)
    {
        return $this->Resolve($id);
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        return true;
    }
}