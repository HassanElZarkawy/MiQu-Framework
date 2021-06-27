<?php

namespace Miqu\Helpers {

    use Miqu\Core\Environment;
    use Tightenco\Collect\Support\Collection;

    function collect($data): Collection
    {
        return new Collection($data ?: []);
    }

    /**
     * gets an environment value for a specific key
     * @param string environment key
     * @param mixed default value if no key was present
     * @return array|mixed
     */
    function env(string $key, $default = null)
    {
        return Environment::get($key, $default);
    }
}
