<?php /** @noinspection PhpDocMissingThrowsInspection */

/** @noinspection PhpUnhandledExceptionInspection */

namespace Miqu\Core;

use Exception;
use Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface;
use Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException;

class CacheManager
{
    private static $cacheInstance;

    /**
     * @return ExtendedCacheItemPoolInterface
     */
    private static function getInstance(): ExtendedCacheItemPoolInterface
    {
        if (self::$cacheInstance === null)
            self::$cacheInstance = \Phpfastcache\CacheManager::getInstance( \Miqu\Helpers\env('cache.driver') );

        return self::$cacheInstance;
    }

    /**
     * @param string $key
     * @param int $expires
     * @param callable $callback
     * @return mixed
     */
    public static function remember(string $key, int $expires, callable $callback)
    {
        if ( ! \Miqu\Helpers\env( 'cache.enabled' ) )
            return call_user_func( $callback );

        $instance = self::getInstance();
        $cachedObject = $instance->getItem($key);
        if ( ! $cachedObject->isHit() )
        {
            $data = call_user_func($callback);
            if ( ! $data )
                throw new Exception( 'Remember callback must have a return value' );
            $cachedObject->set($data)->expiresAfter( $expires );
            $instance->save($cachedObject);
            return $data;
        }

        return $cachedObject->get();
    }

    /**
     * @param string $key
     * @return mixed|null
     * @throws PhpfastcacheInvalidArgumentException
     */
    public static function get(string $key)
    {
        if ( ! \Miqu\Helpers\env( 'cache.enabled' ) )
            return null;

        $cachedObject = self::getInstance()->getItem($key);
        if ( $cachedObject->isHit() )
            return $cachedObject->get();
        return null;
    }

    /**
     * @param string $key
     * @return bool
     * @throws Exception
     */
    public static function delete(string $key): bool
    {
        if ( ! \Miqu\Helpers\env( 'cache.enabled' ) )
            return false;

        return self::getInstance()->deleteItem($key);
    }

    /**
     * @return bool
     */
    public static function clear(): bool
    {
        if ( ! \Miqu\Helpers\env( 'cache.enabled' ) )
            return false;

        return self::getInstance()->clear();
    }
}