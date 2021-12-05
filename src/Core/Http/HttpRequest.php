<?php

namespace Miqu\Core\Http;

use Exception;
use Laminas\Diactoros\ServerRequest;

class HttpRequest extends ServerRequest
{
    public function __construct(array $serverParams = [], array $uploadedFiles = [], $uri = null, string $method = null, $body = 'php://input', array $headers = [], array $cookies = [], array $queryParams = [], $parsedBody = null, string $protocol = '1.1')
    {
        parent::__construct($serverParams, $uploadedFiles, $uri, $method, $body, $headers, $cookies, $queryParams, $parsedBody, $protocol);
    }

    /**
     * @throws Exception
     */
    public function __get(string $name)
    {
        if (property_exists($this, $name))
            return $this->{$name};

        if ( $this->has($name) )
            return $this->input($name);

        return null;
    }

    public function has(string $field) : bool
    {
        return collect($_POST)->merge($_GET)->contains(function($item, $key) use($field) {
            return $key === $field;
        });
    }

    private function input(string $field)
    {
        return collect($_POST)->merge($_GET)->first(function($value, $key) use($field) {
            return $key === $field;
        });
    }
}