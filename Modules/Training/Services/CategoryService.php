<?php

namespace Modules\Training\Services;

use App\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Training\Entities\Category;
use Modules\Training\DTOs\CategoryDTO;

class CategoryService extends BaseService
{
    public const CACHE_TAG = 'categories';

    private const CACHE_TTL = 86400; // 1 day

    public function all(): LengthAwarePaginator
    {
        $cacheKey = $this->generateCacheKey(request()->query(), 'list');

        return Cache::tags(self::CACHE_TAG)->remember($cacheKey, self::CACHE_TTL, function () {
            return Category::query()->filter()->paginate($this->getPerPage());
        });
    }

    public function find(int $id): Category
    {
        $cacheKey = $this->generateCacheKey(['id' => $id], 'item');

        return Cache::tags(self::CACHE_TAG)->remember($cacheKey, self::CACHE_TTL, function () use ($id) {
            return Category::findOrFail($id);
        });
    }

    public function findWithoutRelations(int $id): Category
    {
        $cacheKey = $this->generateCacheKey(['id' => $id], 'item-without-relations');

        return Cache::tags(self::CACHE_TAG)->remember($cacheKey, self::CACHE_TTL, fn() => Category::findOrFail($id));
    }

    public function store(CategoryDTO $dto): Category
    {
        return DB::transaction(function () use ($dto): Category {
            $category = Category::create([
                'name' => $dto->name,
                'type' => $dto->type,
            ]);

            $this->clearCache(); 
            return $category;
        });
    }

    public function update(Category $category, CategoryDTO $dto): Category
    {
        return DB::transaction(function () use ($category, $dto): Category {
            $category->update([
                'name' => $dto->name,
            ]);

            $this->clearCache();
            return $category->fresh();
        });
    }

    public function destroy(Category $category): void
    {
        DB::transaction(function () use ($category): void {
            $category->delete();
            $this->clearCache();
        });
    }
}