<?php

namespace Miqu\Core\Views\DataTables;

use Illuminate\Database\Eloquent\Builder;

trait SelectsColumns
{
    public function select(Builder $query) : Builder
    {
        collect($this->request_columns)->merge($this->columns)->each(function($item) use($query) {
            $string = string($item);
            if ($string->toLowerCase()->startsWith('count:'))
                $this->handleCountableColumns($query, $item);
            else if ($string->contains('.'))
                $this->handleRelationalColumn($query, $item);
        });

        return $query;
    }

    private function handleCountableColumns(Builder $query, string $column)
    {
        $relation = explode(':', $column)[1];
        $query->withCount($relation);
    }

    private function handleRelationalColumn(Builder $query, string $column)
    {
        $relation = explode('.', $column)[0];
        $query->with($relation);
    }
}