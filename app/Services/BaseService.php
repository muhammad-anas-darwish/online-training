<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

abstract class BaseService
{
    /**
     * The cache tag for the service.
     * This should be overridden in child classes.
     *
     * @var string
     */
    public const CACHE_TAG = null;
    
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

    
    /**
     * Clear all cache associated with the service's cache tag.
     *
     * @return void
     * @throws \RuntimeException If CACHE_TAG is not defined in the child class
     */
    protected function clearCache(): void
    {
        if (!defined('static::CACHE_TAG') || static::CACHE_TAG === null) {
            throw new \RuntimeException('CACHE_TAG constant must be defined in ' . static::class);
        }
        
        Cache::tags(static::CACHE_TAG)->flush();
    }

    /**
     * Get the pagination value from the request or use the default.
     *
     * @param int $default
     * @return int
     */
    protected function getPerPage(int $default = 15): int
    {
        return (int) request()->get('per_page', $default);
    }

    /**
     * Generate a unique cache key based on given parameters.
     *
     * @param array $params
     * @param string $prefix
     * @return string
     */
    protected function generateCacheKey(array $params, string $prefix): string
    {
        // CacheSort the array by key to ensure that ['a' => 1, 'b' => 2] and ['b' => 2, 'a' => 1]
        // produce the same cache key.
        ksort($params);
        
        $queryString = http_build_query($params);

        return static::CACHE_TAG . '.' . $prefix . '.' . md5($queryString);
    }
}