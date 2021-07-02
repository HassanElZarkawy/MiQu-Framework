<?php

namespace Miqu\Core\Views\DataTables;

trait UnderstandsDataTablesRequest
{
    /**
     * @var int
     */
    public $draw;

    /**
     * @var int
     */
    public $start = 0;

    /**
     * @var int
     */
    public $length = 50;

    /**
     * @var array
     */
    public $request_columns = [];

    /**
     * @var array
     */
    public $column_search = [];

    /**
     * @var string
     */
    public $orderBy;

    /**
     * @var string
     */
    public $direction;

    /**
     * @var string
     */
    public $search;

    public function processRequest()
    {
        $data = request()->getParsedBody();

        $this->start = $data[ 'start' ];

        $this->length = intval($data[ 'length' ]) === -1 ? 99999 : intval( $data[ 'length' ] );
        if ( $this->length === 0 )
            $this->length = 50;

        $this->request_columns = collect($data[ 'columns' ])->filter(function($item) {
            return !empty($item['data']);
        })->map(function($item) {
            return $item[ 'data' ];
        })->all();

        $this->search = isset( $data[ 'search' ] ) ? ( $data[ 'search' ][ 'value' ] ?? null ) : null;

        $this->draw = $data[ 'draw' ] ?? [];

        $this->customQueries = $data['additional'] ?? [];

        $this->manageOrderColumn( $data );

        if ( isset( $data[ 'phrase' ] ) )
        {
            collect($data['phrase'])->each(function($search, $column) {
                $this->column_search[ $column ] = $search;
            });
        }
    }

    private function manageOrderColumn(?array $data)
    {
        if ( ! is_null( $data ) && isset( $data[ 'order' ] ) )
        {
            $single = $data[ 'order' ][ 0 ];
            $this->orderBy = $single[ 'column' ];
            if ( is_numeric( $this->orderBy ) )
                $this->orderBy = $this->instance->getKeyName();
            $this->direction = $single[ 'dir' ];
        }
        else
        {
            $this->orderBy = $this->instance->getKeyName();
            $this->direction = 'DESC';
        }
    }
}