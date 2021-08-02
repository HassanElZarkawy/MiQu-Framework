<?php

namespace Miqu\Core\Views\DataTables\Contracts;

interface IDataTable
{
    public function process(): IDataTableResults;
}