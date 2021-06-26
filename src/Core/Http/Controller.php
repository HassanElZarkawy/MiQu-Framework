<?php

namespace Miqu\Core\Http;

use League\Route\Http\Exception\BadRequestException;

class Controller
{
    /**
     * @param string $method
     * @param array $parameters
     * @throws BadRequestException
     */
    public function __call(string $method, array $parameters)
    {
        $uri = request()->getUri()->getPath();
        $controller = static::class;

        logger('http')->critical(
            "$uri is mapped to unknown function in $controller",
            [
                'controller' => $controller,
                'method' => $method
            ]
        );

        throw new BadRequestException(
            sprintf(
                'Method %s::%s does not exist.', $controller, $method
            )
        );
    }
}