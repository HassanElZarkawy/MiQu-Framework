<?php

namespace Miqu\Core\Views\DataTables\Contracts;

interface IDataTableResults
{
    public function setDraw(int $draw): self;

    public function getDraw(): int;

    public function setTotalRecords(int $total_records): self;

    public function getTotalRecords(): int;

    public function setRecordsFiltered(int $number): self;

    public function getRecordsFiltered(): int;

    public function setData(array $data): self;

    public function getData(): array;

    public function toArray(): array;
}