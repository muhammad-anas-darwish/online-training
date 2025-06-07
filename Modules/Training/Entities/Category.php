<?php

namespace Modules\Training\Entities;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends BaseModel
{
    /** @use HasFactory<\Database\Factories\Modules\Training\Entities\CategoryFactory> */
    use HasFactory;

    protected $table = 'training_categories';
    
    protected $fillable = [
        'name', 
        'type',
    ];

    protected static $filterableColumns = [
        'type',
        'name',
    ];

    protected static $searchableColumns = [
        'name',
    ];
}