<?php

namespace Miqu\Core\Views\DataTables;

use Miqu\Core\Views\DataTables\Contracts\IDataTableResults;

class DataTableResults implements IDataTableResults
{
    /**
     * @var int
     */
    public $draw = 1;

    /**
     * @var array
     */
    public $data = [];

    /**
     * @var int
     */
    public $recordsTotal = 0;

    /**
     * @var int
     */
    public $recordsFiltered = 0;

    public function setDraw(int $draw): IDataTableResults
    {
        $this->draw = $draw;
        return $this;
    }

    public function getDraw(): int
    {
        return $this->draw;
    }

    public function setTotalRecords(int $total_records): IDataTableResults
    {
        $this->recordsTotal = $total_records;
        return $this;
    }

    public function getTotalRecords(): int
    {
        return $this->recordsTotal;
    }

    public function setRecordsFiltered(int $number): IDataTableResults
    {
        $this->recordsFiltered = $number;
        return $this;
    }

    public function getRecordsFiltered(): int
    {
        return $this->recordsTotal;
    }

    public function setData(array $data): IDataTableResults
    {
        $this->data = $data;
        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return [
            'draw' => $this->draw,
            'recordsTotal' => $this->recordsTotal,
            'recordsFiltered' => $this->recordsFiltered,
            'data' => $this->data,
        ];
    }
}