<?php

namespace Miqu\Core\Views;

use Illuminate\Database\Eloquent\Builder;
use Tightenco\Collect\Support\Collection;

trait HandlesCustomQueries
{
    /**
     * @var array
     */
    public $customQueries = [];

    public function handleCustomQueries() : void
    {
        if ( count( $this->customQueries ) === 0 )
            return;

        $simple = collect($this->customQueries)->filter(function($value, $key) {
            return !(string($key)->contains('.'));
        });

        $this->handleSimpleQueries($simple);

        $complex = collect($this->customQueries)->filter(function($value, $key) {
            return string($key)->contains('.');
        });

        $this->handleComplexQueries($complex);
    }

    private function handleSimpleQueries(Collection $simple)
    {
        $this->instance->where(function(Builder $query) use($simple) {
            $simple->each(function($value, $key) use($query) {
                if (string($value)->contains('|'))
                    collect(explode('|', $value))->each(function($item) use ($key, $query) {
                        $query->orWhere($key, $item);
                    });
                else
                    $query->where($key, $value);
            });
            return $query;
        });
    }

    private function handleComplexQueries(Collection $complex)
    {
        collect($complex)->each(function($value, $key) {
            $relation_column = explode( '.', $key );
            $relation = $relation_column[0];
            $column = $relation_column[1];
            $this->instance->whereHas($relation, function($query) use( $column, $value ) {
                $query->where($column, $value);
            });
        });
    }
}