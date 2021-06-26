<?php

namespace Miqu\Core\Views;

trait FormatsResults
{
    public function format(array $results) : array
    {
        $this->formatCountableColumns($results);
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
}