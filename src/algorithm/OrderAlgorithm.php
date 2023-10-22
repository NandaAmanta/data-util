<?php

namespace Amanta\DataUtil\Algorithm;

use Illuminate\Database\Eloquent\Collection;

class OrderAlgorithm {

     /**
     * Sorting the data algorithm
     */
    public static function orderAlgorithm($query, $column = 'created_at', $relation = null, $orderType = "DESC")
    {
        if (is_null($orderType)) {
            $orderType = "DESC";
        }
        if (is_null($column) || strlen($column) < 1) {
            $column = 'created_at';
        }

        if ($query instanceof Collection) {
            return $query->sortBy($column, descending: strtoupper($orderType) == 'DESC');
        }

        $model = $query->getModel();
        $stringColumn = $column;

        if (!is_null($relation) &&  strlen($relation) > 0) {
            return $query->get()->sortBy($relation . '.' . $stringColumn, descending: strtoupper($orderType) == 'DESC');
        }

        if (!in_array($stringColumn, $model->getFillable()) && !in_array($stringColumn, $model->getDates())) {
            if (!in_array($stringColumn, $model->getAppends())) {
                $stringColumn = 'created_at';
            }

            if (strtoupper($orderType)  == 'DESC') {
                return $query->get()->sortByDesc(function ($result) use ($relation, $stringColumn) {
                    if (!is_null($relation)) {
                        return $result->$relation->$stringColumn;
                    }
                    return $result->$stringColumn;
                });
            }

            return $query->get()->sortBy(function ($result) use ($relation, $stringColumn) {
                if (!is_null($relation)) {
                    return $result->$relation->$stringColumn;
                }
                return $result->$stringColumn;
            });
        }

        return $query->orderBy($column, $orderType);
    }

}