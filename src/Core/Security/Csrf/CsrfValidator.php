<?php

namespace Miqu\Core\Security\Csrf;

class CsrfValidator implements ICsrfValidator
{
    private $tokenKey;

    public function __construct()
    {
        $this->tokenKey = 'csrf_token';
    }

    /**
     * @return bool
     */
    public function validate() : bool
    {
        if ( strtolower( request()->getMethod() ) !== 'post' )
            return false;

        $body = request()->getParsedBody();

        if ( ! array_key_exists( $this->tokenKey, $body ) )
            return false;

        $token = $body[ $this->tokenKey ];

        return validate_csrf( $token );
    }
}