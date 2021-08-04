<?php

namespace Miqu\Core\Views\DataTables;

use Miqu\Core\CacheManager;
use Exception;
use Illuminate\Database\Capsule\Manager;

trait ExtractsColumns
{
    /**
     * @var array
     */
    public $columns = [];

    /**
     * @return array
     * @throws Exception
     */
    public function extractColumns() : array
    {
        $cache_key = (string)string(get_class($this->instance))->replace('\\', '_');
        return CacheManager::remember($cache_key, 3600, function() {
            $table = $this->instance->getTable();
            return Manager::schema()->getColumnListing($table);
        });
    }
}