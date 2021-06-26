<?php

namespace Miqu\Core;

use Miqu\Core\Http\HeadersBag;
use Carbon\Carbon;
use Exception;
use HansOtt\PSR7Cookies\InvalidArgumentException;
use HansOtt\PSR7Cookies\SetCookie;
use Models\Security\Token;
use Models\User;
use Psr\Http\Message\ServerRequestInterface;
use function Miqu\Helpers\logger;

class Authentication
{
    /**
     * @var bool
     */
    private static $checked_before = false;

    /**
     * @var User
     */
    private static $user = null;

    /**
     * @var string
     */
    private $cookieName = 'auth_token';

    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var int
     */
    private $expires_after = 3600;

    /**
     * @var int
     */
    private $expires_after_extended = 3600 * 24;

    public function __construct()
    {
        $this->request = request();
    }

    public static function __callStatic(string $method, array $args)
    {
        return (new static)->{$method}($args);
    }

    public function attempt(string $login, string $password, bool $remember = false): bool
    {
        try {
            if ($this->check())
                return true;
        } catch (Exception $e) {
            // fail silently
        }

        if ( empty($login) || empty($password) )
            return false;

        /** @var ?User $user */
        $user = User::where(function($query) use ($login) {
            return $query->where('username', $login)->orWhere('email', $login);
        })->where('status', 'active')->first();

        if ( $user === null )
            return false;

        if ( ! password_verify( $password, $user->password ) )
            return false;

        try {
            $token = $this->createTokenForUser($user->id, $remember);
            $this->setAuthCookie($token);
            self::$user = $user;
            self::$checked_before = true;
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function check() : bool
    {
        if (self::$checked_before)
            return true;

        if ( ! $this->tokenPresent() )
            return false;

        $cookie = $this->getAuthCookie();
        $token = $this->getTokenRecordFromString($cookie);

        if ( $token === null )
            return false;

        if ( $token->expires_at->isPast() )
        {
            $token->delete();
            return false;
        }

        $this->updateTokenExpiry($token);
        $this->setAuthCookie($token);

        self::$checked_before = true;
        self::$user = $this->getUserFromToken($token);
        return true;
    }

    public function user() : ?User
    {
        try {
            if (!$this->check())
                return null;
        } catch (Exception $exception) {
            // fail silently
        }

        if (self::$user !== null)
            return self::$user;

        $token = $this->getAuthCookie();
        $user = $this->getUserFromToken($token);

        if ( $user == null )
            return null;
        self::$user = $user;
        return $user;
    }

    public function id()
    {
        if (self::$user)
            return self::$user->id;

        try {
            if ($this->check())
                return $this->user()->id;
        } catch (Exception $exception) {
            if ( \Miqu\Helpers\env('logger.enabled') )
                logger()->warning('Calling auth()->id() when not authenticated');
        }

        return null;
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function logout() : bool
    {
        try {
            if (!$this->check())
                return true;
        } catch (Exception $exception) {
            // fail silently
        }

        $token = $this->getAuthCookie();
        $record = $this->getTokenRecordFromString($token);
        $record->delete();
        $this->setEmptyAuthCookie();
        self::$checked_before = false;
        self::$user = null;
        return true;
    }

    private function tokenPresent() : bool
    {
        try {
            $cookies = $this->request->getCookieParams();
            return array_key_exists( $this->cookieName, $cookies );
        } catch(Exception $exception) {
            if (env('logger.enabled'))
                logger()->warning('Unable to get Cookie params from request object at Authentication::tokenPresent()');
            return false;
        }
    }

    /**
     * @throws Exception
     */
    private function createTokenForUser(int $user_id, bool $extended) : Token
    {
        Token::where('user_id', $user_id)->delete();

        $token = bin2hex( random_bytes( 32 ) );

        $expiry_date = time() + $this->expires_after;

        if( $extended )
            $expiry_date = time() + $this->expires_after_extended;

        return Token::create([
            'user_id' => $user_id,
            'token' => $token,
            'expires_at' => date( 'Y-m-d H:i:s', $expiry_date ),
            'extended' => $extended ? 1 : 0
        ]);
    }

    private function getAuthCookie() : string
    {
        $cookies = $this->request->getCookieParams();
        return $cookies[ $this->cookieName ];
    }

    /**
     * @param string $token
     * @return Token|null
     */
    private function getTokenRecordFromString(string $token) : ?Token
    {
        return Token::where('token', $token)->first();
    }

    private function updateTokenExpiry(Token $token)
    {
        $expiry = $this->expires_after;
        if ($token->extended)
            $expiry = $this->expires_after_extended;
        $token->update([
            'expires_at' => Carbon::now()->addSeconds($expiry)
        ]);
    }

    /**
     * @throws InvalidArgumentException
     */
    private function setAuthCookie(Token $record) : void
    {
        if ( ! $record->token )
            return;

        CookiesBag::add(new SetCookie(
            $this->cookieName,
            $record->token,
            Carbon::now()->addSeconds($record->extended ? $this->expires_after : $this->expires_after_extended)->timestamp,
            '/'
        ));
        $_COOKIE[$this->cookieName] = $record->token;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function setEmptyAuthCookie()
    {
        if ( isset( $_COOKIE[$this->cookieName] ) )
            unset($_COOKIE[$this->cookieName]);

        CookiesBag::add(new SetCookie(
            $this->cookieName,
            '',
            Carbon::now()->subSeconds(3600)->timestamp,
            '/'
        ));
    }

    private function getUserFromToken(string $token) : ?User
    {
        return User::whereHas('token', function($query) use ($token) {
            $query->where('token', $token);
        })->first();
    }
}