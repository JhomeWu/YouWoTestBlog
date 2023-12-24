<?php

namespace App\Utils;

class SearchData
{
    public $columns = ['*'];
    public $whereColumns = [];
    public $sortBy = 'id';
    public $order = 'asc';
    public $page = 1;
    public $numberPerPage = 10;
    public $countBy = '*';
    public $relation = [];

    public function __construct(array $searchConditions)
    {
        $this->columns = $searchConditions['columns'] ?? $this->columns;
        $this->whereColumns = $searchConditions['whereColumns'] ?? $this->whereColumns;
        $this->sortBy = $searchConditions['sortBy'] ?? $this->sortBy;
        $this->order = $searchConditions['order'] ?? $this->order;
        $this->page = $searchConditions['page'] ?? $this->page;
        $this->numberPerPage = $searchConditions['numberPerPage'] ?? $this->numberPerPage;
        $this->countBy = $searchConditions['countBy'] ?? $this->countBy;
        $this->relation = $searchConditions['relation'] ?? $this->relation;
    }
}
