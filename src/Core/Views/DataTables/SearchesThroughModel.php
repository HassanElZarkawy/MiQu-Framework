<?php

namespace Miqu\Core\Views\DataTables;

use Illuminate\Database\Eloquent\Builder;

trait SearchesThroughModel
{
    public function searchInAttributes()
    {
        if ( ! empty( $this->search ) && ! is_null( $this->search ) && strlen( $this->search ) > 0 )
            $this->performGlobalSearch();

        if ( count( $this->column_search ) !== 0 )
            $this->performColumnSearch();
    }

    private function performGlobalSearch()
    {
        $this->instance->where(function(Builder $query) {
            collect($this->columns)->each(function($column) use ($query) {
                $query->where($column, 'LIKE', "%{$this->search}%");
            });
        });
    }

    private function performColumnSearch()
    {
        $this->instance->where(function(Builder $query) {
            collect($this->column_search)->each(function($search, $column) use($query) {
                if ( ! in_array( $column, $this->columns ) || strlen( $search ) === 0 )
                    return;
                $query->where($column, 'LIKE', "{$search}%");
            });
        });
    }
}