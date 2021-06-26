<?php /** @noinspection PhpDocMissingThrowsInspection */

/** @noinspection PhpUnhandledExceptionInspection */

namespace Miqu\Core;

use Exception;
use Phpfastcache\Exceptions\PhpfastcacheDriverCheckException;
use Phpfastcache\Exceptions\PhpfastcacheDriverException;
use Phpfastcache\Exceptions\PhpfastcacheDriverNotFoundException;
use Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException;
use Phpfastcache\Exceptions\PhpfastcacheInvalidConfigurationException;

class CacheManager
{
    /**
     * @param string $key
     * @param callable $callback
     * @param int $expires
     * @return mixed
     */
    public static function remember(string $key, callable $callback, int $expires)
    {
        $instance = \Phpfastcache\CacheManager::getInstance( \Miqu\Helpers\env('cache.driver') );
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
     * @throws PhpfastcacheDriverCheckException
     * @throws PhpfastcacheDriverException
     * @throws PhpfastcacheDriverNotFoundException
     * @throws PhpfastcacheInvalidArgumentException
     * @throws PhpfastcacheInvalidConfigurationException
     */
    public static function get(string $key)
    {
        $instance = \Phpfastcache\CacheManager::getInstance('files');
        $cachedObject = $instance->getItem($key);
        if ( $cachedObject->isHit() )
            return $cachedObject->get();
        return null;
    }

    /**
     * @param string $key
     * @return bool
     * @throws PhpfastcacheDriverCheckException
     * @throws PhpfastcacheDriverException
     * @throws PhpfastcacheDriverNotFoundException
     * @throws PhpfastcacheInvalidArgumentException
     * @throws PhpfastcacheInvalidConfigurationException
     */
    public static function delete(string $key): bool
    {
        $instance = \Phpfastcache\CacheManager::getInstance('files');
        return $instance->deleteItem($key);
    }

    /**
     * @return bool
     * @throws PhpfastcacheDriverCheckException
     * @throws PhpfastcacheDriverException
     * @throws PhpfastcacheDriverNotFoundException
     * @throws PhpfastcacheInvalidArgumentException
     * @throws PhpfastcacheInvalidConfigurationException
     */
    public static function clear(): bool
    {
        $instance = \Phpfastcache\CacheManager::getInstance('files');
        return $instance->clear();
    }
}