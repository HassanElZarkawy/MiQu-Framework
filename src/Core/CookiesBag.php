<?php

namespace Miqu\Core;

use Miqu\Core\Http\HttpResponse;
use HansOtt\PSR7Cookies\SetCookie;

class CookiesBag
{
    /**
     * @var SetCookie[]
     */
    private static $cookies;

    public static function add(SetCookie $cookie)
    {
        self::$cookies[] = $cookie;
    }

    public static function addToResponse(HttpResponse $response)
    {
        \Miqu\Helpers\collect(self::$cookies)->each(function(SetCookie $cookie) use ($response) {
            $response->setCookie(
                $cookie->getName(),
                $cookie->getValue(),
                $cookie->expiresAt(),
                $cookie->getPath(),
                $cookie->isSecure(),
                $cookie->isHttpOnly()
            );
        });
    }
}