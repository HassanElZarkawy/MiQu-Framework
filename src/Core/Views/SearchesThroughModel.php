<?php

namespace Miqu\Core\Views;

use Illuminate\Database\Query\Builder;

trait SearchesThroughModel
{
    public function searchInAttributes()
    {
        if ( empty( $this->search ) || is_null( $this->search ) )
            return;

        $this->instance->where(function(Builder $query) {
            collect($this->columns)->each(function($column) use ($query) {
                $query->where($column, 'LIKE', "%{$this->search}%");
            });
        });
    }
}