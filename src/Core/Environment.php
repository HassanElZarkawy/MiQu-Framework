<?php /** @noinspection PhpUnused */

namespace Miqu\Core;

use ArrayAccess;
use Exception;

class Environment
{
    /**
     * Key-value storage.
     *
     * @var array
     */
    protected static $variables = [];

    /**
     * Required variables.
     *
     * @var array
     */
    protected static $required = [];

    /**
     * Were variables loaded?
     *
     * @var bool
     */
    protected static $isLoaded = false;

    /**
     * Load .env.php file or array.
     *
     * @param string|array $source
     *
     * @return void
     * @throws Exception
     */
    public static function load($source)
    {
        self::$variables = is_array($source) ? $source : require $source;
        self::$isLoaded = true;
    }

    /**
     * Copy all variables to putenv().
     *
     * @param string $prefix
     */
    public static function copyVarsToPutenv(string $prefix = 'PHP_')
    {
        foreach (self::all() as $key => $value) {
            if (is_object($value) || is_array($value)) {
                $value = serialize($value);
            }

            putenv($prefix . $key . '=' . $value);
        }
    }

    /**
     * Copy all variables to $_ENV.
     */
    public static function copyVarsToEnv()
    {
        foreach (self::all() as $key => $value) {
            $_ENV[$key] = $value;
        }
    }

    /**
     * Copy all variables to $_SERVER.
     */
    public static function copyVarsToServer()
    {
        foreach (self::all() as $key => $value) {
            $_SERVER[$key] = $value;
        }
    }

    /**
     * Get env variables.
     *
     * @return array
     */
    public static function all() : array
    {
        return self::$variables;
    }

    /**
    * Get an item from an array using "dot" notation.
    *
    * @param  string  $key
    * @param  mixed   $default
    * @return mixed
    */
    public static function get(string $key, $default = null)
    {
        $array = self::$variables;

//        if (! static::accessible($array)) {
//            return value($default);
//        }
        if (static::exists($array, $key)) {
            return $array[$key];
        }
        if (strpos($key, '.') === false) {
            return $array[$key] ?? value($default);
        }
        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return value($default);
            }
        }

        return $array;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return array
     */
    public static function set(string $key, $value): array
    {
        $array = &self::$variables;

        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Set env variable.
     *
     * @param string|array $keys
     * @param mixed        $value
     *
     * @return void
     */
    // public static function set(string $key, $value = null)
    // {
    //     self::set_nested_array_value( $key, $value );
    //     dump( self::$variables ); die();
    //     if (is_array($keys)) {
    //         self::$variables = array_merge(self::$variables, $keys);
    //     } else {
    //         //self::$variables[$keys] = $value;

    //     }
    // }

    /**
     * Determine whether the given value is array accessible.
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function accessible($value): bool
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param  ArrayAccess|array  $array
     * @param  string|int  $key
     * @return bool
     */
    public static function exists($array, $key): bool
    {
        if ($array instanceof ArrayAccess)
        {
            return $array->offsetExists($key);
        }
        return array_key_exists($key, $array);
    }

    /**
     * Delete all variables.
     *
     * @return void
     */
    public static function flush()
    {
        self::$variables = [];
        self::$isLoaded = false;
    }
}