<?php

namespace Amanta\DataUtil\Eloquent;

use \Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class Model extends EloquentModel
{
    /**
     * Scope Search 
     * example : 
     * ['username', 'name', 'instance', 'email']
     */
    public static $scopeSearch = [];

    /**
     * Scope Filter 
     * example : 
     * [
     *    [query => 'permission_id', column => 'permissions-_id'],
     *    [query => 'name', column => 'name'],
     *    [query => 'email', filter => 'filterEmail'],
     * ]
     */
    public static $scopeFilter = [];
}
