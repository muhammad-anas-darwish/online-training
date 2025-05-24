<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

abstract class BaseService
{
    /**
     * Determine whether to bypass cache
     */
    protected function shouldBypassCache(Model|string $model): bool
    {
        if (request()->has('search')) {
            return true;
        }
    
        $allFilterableColumns = array_merge(
            $model::getFilterableColumns(),
            $model::getMultiFilterableColumns(),
            $model::getDateFilterableColumns()
        );
    
        return request()->hasAny($allFilterableColumns);
    }
}