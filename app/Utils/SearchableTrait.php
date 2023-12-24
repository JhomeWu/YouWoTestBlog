<?php

namespace App\Utils;

trait SearchableTrait
{
    public static function appendWhereQuery($query, SearchData $searchData)
    {
        foreach ($searchData->whereColumns as $whereColumn) {
            if (count($whereColumn) > 0) {
                [$column, $operator, $value] = $whereColumn;
                if (! in_array($column, static::$searchableColumns)) {
                    continue;
                }
                if ($operator === 'in' && is_array($value)) {
                    $query->whereIn($column, $value);
                } elseif ($operator === 'notin' && is_array($value)) {
                    $query->whereNotIn($column, $value);
                } elseif ($operator === 'like') {
                    $fuzzySearch = implode('%', str_split($value));
                    $query->where($column, 'like', $fuzzySearch);
                } else {
                    $query->where($column, $operator, $value);
                }
            }
        }
    }

    public static function search($query, SearchData $searchData)
    {
        $query->select($searchData->columns);
        $query->with($searchData->relation);
        static::appendWhereQuery($query, $searchData);
        $query->orderBy($searchData->sortBy, $searchData->order);
        $pageSize = min($searchData->numberPerPage, static::$maxPerPage);
        $query->limit($pageSize + 1);
        $query->offset(($searchData->page - 1) * $pageSize);

        $data = $query->get();

        return [
            'data' => $data->take($pageSize),
            'hasNext' => $data->count() > $pageSize,
        ];
    }

    public static function getValidationRule()
    {
        $rule = [];
        $rule['whereColumns'] = 'nullable|array';
        $rule['whereColumns.*'] = 'array';
        $rule['whereColumns.*.0'] = 'required|string|in:' . implode(',', static::$searchableColumns);
        $rule['whereColumns.*.1'] = 'required|string|in:=,!=,>,<,>=,<=,like,in,notin';
        $rule['whereColumns.*.2'] = 'required|string';
        $rule['sortBy'] = 'nullable|in:' . implode(',', static::$sortableColumns);
        $rule['order'] = 'nullable|in:asc,desc';
        $rule['page'] = 'nullable|integer|min:1';
        $rule['numberPerPage'] = 'nullable|integer|min:1|max:' . static::$maxPerPage;
        $rule['countBy'] = 'nullable|string';

        return $rule;
    }
}
