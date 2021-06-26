<?php

namespace Miqu\Core\Http;

use HansOtt\PSR7Cookies\SetCookie;

class HeadersBag
{
    /**
     * @var array
     */
    protected static $headers = [];

    /**
     * Adds a header to the headers bag
     * @param string $name
     * @param string|string[] $value
     */
    public static function add(string $name, $value) : void
    {
        self::$headers[ $name ] = $value;
    }

    /**
     * Remove a header from the headers bag by it's name (key)
     * @param string $name
     */
    public static function remove(string $name) : void
    {
        if ( array_key_exists( $name, self::$headers ) )
            unset( self::$headers[ $name ] );
    }

    /**
     * Clears the current headers bag
     */
    public static function clear() : void
    {
        self::$headers = [];
    }

    /**
     * Checks if a header exists in the headers bag
     * @param string $name
     * @return bool
     */
    public static function has(string $name) : bool
    {
        return array_key_exists( $name, self::$headers );
    }

    /**
     * Adds a SetCookie object to the Set-Cookie header
     * @param SetCookie $cookie
     */
    public static function cookie(SetCookie $cookie) : void
    {
        self::$headers[ 'Set-Cookie' ] = $cookie->toHeaderValue();
    }

    /**
     * Adds all the added headers to the response object
     * @param HttpResponse $response
     * @return HttpResponse
     */
    public static function addToResponse(HttpResponse $response): HttpResponse
    {
        if ( count( self::$headers ) > 0 )
        {
            foreach( self::$headers as $name => $value )
            {
                $response = $response->withHeader( $name, $value );
            }
        }


        return $response;
    }
}