<?php

namespace Miqu\Core\Views\DataTables;

use Illuminate\Database\Eloquent\Builder;
use Miqu\Core\Models\User;

trait SelectsColumns
{
    private $joins = [];

    public function select(Builder $query) : Builder
    {
        collect($this->request_columns)->merge($this->columns)->each(function($item) use($query) {
            $string = string($item);
            if ($string->toLowerCase()->startsWith('count:'))
                $this->handleCountableColumns($query, $item);
            else if ($string->toLowerCase()->startsWith('max:'))
                $this->handleMaxColumns($query, $item);
            else if ($string->toLowerCase()->startsWith('min:'))
                $this->handleMinColumns($query, $item);
            else if ($string->toLowerCase()->startsWith('sum:'))
                $this->handleSumColumns($query, $item);
            else if ($string->toLowerCase()->startsWith('avg:'))
                $this->handleAverageColumns($query, $item);
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

    private function handleMaxColumns(Builder $query, string $column)
    {
        $this->addSelect($query, 'max', $column);
    }

    private function handleMinColumns(Builder $query, string $column)
    {
        $this->addSelect($query, 'min', $column);
    }

    private function handleSumColumns(Builder $query, string $column)
    {
        $this->addSelect($query, 'sum', $column);
    }

    private function handleAverageColumns(Builder $query, string $column)
    {
        $this->addSelect($query, 'avg', $column);
    }

    private function getRelationTable(string $relation): string
    {
        return $this->instance->{$relation}()->getRelated()->getTable();
    }

    private function relationPrimary(string $relation)
    {
        return $this->instance->{$relation}()->getRelated()->getKeyName();
    }

    private function addSelect(Builder $query, string $select, string $column)
    {
        /** @var string $relation */
        $expression = explode(':', $column)[1];
        $relation = $expression;
        $requires_join = false;
        $column = $relation;

        if ( string($relation)->contains('.') ) // max:tokens.something
        {
            $relation = explode('.', $relation)[0];
            $column = explode('.', $relation)[1];
            $requires_join = true;
        }

        if ( $requires_join && ! in_array( $relation, $this->joins ) )
        {
            $join_table = $this->getRelationTable($relation);
            $join_table_key = $this->relationPrimary($relation);
            $first = $this->instance->getTable() . '.' . $this->instance->getKeyName();
            $second = $join_table . '.' . $join_table_key;
            $query->join($join_table, $first, '=', $second);
            $query->addSelect("$select({$join_table_key}.{$column}) as `{$relation}_{$column}_{$select}`");
            $this->joins[ $relation ] = [
                'table' => $join_table,
                'key' => $join_table_key
            ];
        }
        else
        {
            $query->addSelect("$select($column) as `{$column}_{$select}`");
        }
    }
}