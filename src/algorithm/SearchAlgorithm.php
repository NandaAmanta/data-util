<?php

namespace Amanta\DataUtil\Algorithm;

use Illuminate\Support\Collection as BaseCollection;

class SearchAlgorithm
{
    /**
     *  Searching the data Algorithm
     * 
     *  @param mixed $query the data/colelction/query builder
     *  @param array $scopeSearch array of fields/columns that can be searched
     *  @param string $searchValue requested search value
     *  
     *  @return mixed $query 
     */
    public static function searchAlgorithm($query, $scopeSearch = [], $searchValue = null)
    {
        if (is_null($searchValue) || count($scopeSearch) < 1 || strlen($searchValue) <= 0) {
            return $query;
        }
        if ($query instanceof BaseCollection) {
            $query = $query->filter(function ($item) use ($scopeSearch, $searchValue) {
                foreach ($scopeSearch as $field) {
                    if (str_contains($item[$field], $searchValue)) {
                        return true;
                    }
                }
                return false;
            });
            return $query;
        }
        $query->where(function ($q) use ($scopeSearch, $searchValue) {
            $q->where($scopeSearch[0], 'LIKE', '%' . $searchValue . '%');
            for ($i = 1; $i < count($scopeSearch); $i++) {
                if (str_contains($scopeSearch[$i], '-')) {
                    $splitedStr = explode('-', $scopeSearch[$i]);
                    $field = $splitedStr[count($splitedStr) - 1];
                    array_pop($splitedStr);
                    $q->orWhereHas(join('.', $splitedStr), function ($q) use ($field, $searchValue) {
                        $q->where($field, 'LIKE', '%' . $searchValue . '%');
                    });
                }
                $q->orWhere($scopeSearch[$i], 'LIKE', '%' . $searchValue . '%');
            }
        });
        return $query;
    }
}
