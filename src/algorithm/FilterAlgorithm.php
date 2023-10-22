<?php

namespace Amanta\DataUtil\Algorithm;

class FilterAlgorithm
{
    /**
     *  Filtering the data Algorithm
     * 
     *  @param mixed $query the data/colelction/query builder
     *  @param array $filters requested filter
     *  @param array $scopeFilter array of fields/columns that can be filtered.
     *  Scope Filter 
     *  example : 
     *  [
     *      [query => 'permission_id', column => 'permissions-_id'],
     *      [query => 'name', column => 'name'],
     *  ]
     *  
     *  @return mixed $query 
     */
    public  static function filterAlgorithm($query, $scopeFilter = [], $filters = [])
    {
        $starSymbolExist = false;
        $filterExecuted = [];
        foreach ($scopeFilter as $scope) {
            $queryParam = $scope['query'] ?? $scope['column'];
            $column = $scope['column'] ?? null;
            $value = $filters->get($queryParam);
            if ($column  == '*') {
                $starSymbolExist = true;
                continue;
            }
            // Filter by Custom Filter
            if (isset($scope['filter']) && $filters->has($queryParam) && $filters->get($queryParam) != null) {
                $query = $query->getModel()->{$scope['filter']}($query, $value);
                $filterExecuted[] = $queryParam;
                continue;
            }
            // Filter by normal filter
            if ($filters->has($queryParam) && !is_null($value) && strlen($value) > 0) {
                $filterExecuted[] = $queryParam;
                if ($value == 'true' || $value == 'false') {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }
                if (is_numeric($value)) {
                    $value = (int) $value;
                }
                if (str_contains($column, '-')) {
                    $splitedStr = explode('-', $column);
                    $field = $splitedStr[count($splitedStr) - 1];
                    array_pop($splitedStr);
                    $query->whereHas(join('.', $splitedStr), function ($q) use ($value, $field) {
                        $q->where($field, $value);
                    });
                    continue;
                }
                $query = $query->where($column, $value);
            }
        }

        if ($starSymbolExist) {
            foreach ($filters as $key => $value) {
                $excludedKey = ['order_by', 'order_type', 'order_parent_column', 'search', 'per_page', 'page', 'limit', 'offset', 'fields', 'with', 'current_page'];
                if (in_array($key, $filterExecuted) || in_array($key, $excludedKey)) {
                    continue;
                }
                $query = $query->where($key, $value);
            }
        }

        return $query;
    }
}
