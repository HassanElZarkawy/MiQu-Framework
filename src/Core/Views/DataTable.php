<?php

namespace Miqu\Core\Views;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use ReflectionException;

class DataTable
{
    use ExtractsColumns, HandlesCustomQueries, UnderstandsDataTablesRequest, SearchesThroughModel,
        OrdersModel, SelectsColumns, FormatsResults;

    /**
     * @var Model
     */
    private $instance;

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function __construct(string $abstract)
    {
        global $container;
        $this->instance = $container->Resolve($this->modelClass($abstract));
        $this->columns = $this->extractColumns();
        $this->processRequest();
        $this->instance = $this->instance->newQuery();
    }

    /**
     * @return array
     */
    public function process(): array
    {
        $this->handleCustomQueries();

        $this->searchInAttributes();

        $this->processOrderBy();

        $records = $this->getResults()->all();

        $records = $this->format($records);

        return [
            'draw' => $this->draw,
            'recordsTotal' => $this->instance->count(),
            'recordsFiltered' => count( $records ),
            'data' => $records,
            'chart_data' => null
        ];
    }

    /**
     * @return Collection
     */
    private function getResults(): Collection
    {
        return $this->select($this->instance)->skip($this->start)
            ->take($this->length)
            ->get();
    }

    private function modelClass(string $abstract) : string
    {
        $model = preg_replace('/([a-zA-Z])(?=[A-Z])/',
            '$1\\',
            $abstract);

        try {
            if ( ! class_exists( $model ) )
            {
                $last_item_position = strrpos($model, '\\');
                $model = substr_replace( $model, '', $last_item_position, 1 );
            }
        } catch (Exception $exception) {
            $last_item_position = strrpos($model, '\\');
            $model = substr_replace( $model, '', $last_item_position, 1 );
        }

        return $model;
    }
}