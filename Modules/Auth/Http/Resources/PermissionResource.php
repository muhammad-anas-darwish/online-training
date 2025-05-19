<?php

namespace Modules\Auth\Http\Resources;

use App\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends BaseJsonResource
{
    protected array $baseFields = ['id'];

    protected function getCustomData(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
