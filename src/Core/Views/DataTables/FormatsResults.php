<?php

namespace Miqu\Core\Views\DataTables;

trait FormatsResults
{
    public function format(array $results) : array
    {
        $this->formatCountableColumns($results);
        $this->formatMaxColumns($results);
        $this->formatMinColumns($results);
        $this->formatSumColumns($results);
        $this->formatAverageColumns($results);
        return $results;
    }

    private function formatCountableColumns(array &$results)
    {
        collect($this->request_columns)->filter(function($item) {
            return string($item)->toLowerCase()->startsWith('count:');
        })->each(function($column) use($results) {
            $relation = explode(':', $column)[1];
            collect($results)->each(function($record) use($relation) {
                $property = "count:$relation";
                $value = "{$relation}_count";
                $record->{$property} = $record->{$value};
            });
        });
    }

    private function formatMaxColumns(array & $results)
    {
        $this->syncWithRequestColumns($results, 'max');
    }

    private function formatMinColumns(array & $results)
    {
        $this->syncWithRequestColumns($results, 'min');
    }

    private function formatSumColumns(array & $results)
    {
        $this->syncWithRequestColumns($results, 'sum');
    }

    private function formatAverageColumns(array & $results)
    {
        $this->syncWithRequestColumns($results, 'avg');
    }

    private function syncWithRequestColumns(array &$results, string $operation)
    {
        collect($this->request_columns)->filter(function($item) use($operation) {
            return string($item)->toLowerCase()->startsWith("{$operation}:");
        })->each(function($column) use($results, $operation) {
            $column = explode(':', $column)[1];
            $parts = explode('_', $column);
            if ( count( $parts ) === 2 ) // model column (column_max)
            {
                $column = $parts[0];
                collect($results)->each(function($record) use($column, $operation) {
                    $property = "{$operation}:$column";
                    $value = "{$column}_{$operation}";
                    $record->{$property} = $record->{$value};
                });
            }
            else if ( count( $parts ) === 3 ) // relation column (relation_column_max)
            {
                $column = $parts[0] . '.' . $parts[1];
                collect($results)->each(function($record) use($column, $parts, $operation) {
                    $property = "{$operation}:$column";
                    $value = "{$parts[0]}_{$parts[1]}_{$operation}";
                    $record->{$property} = $record->{$value};
                });
            }
        });
    }
}