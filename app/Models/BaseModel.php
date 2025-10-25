<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseModel extends Model
{
    protected static function booted()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenantId = config('app.current_tenant_id');
            if ($tenantId && method_exists($builder->getModel(), 'getTenantColumn')) {
                $builder->where($builder->getModel()->getTenantColumn(), $tenantId);
            }
        });

        static::creating(function ($model) {
            if (method_exists($model, 'getTenantColumn') && !$model->{$model->getTenantColumn()}) {
                $model->{$model->getTenantColumn()} = config('app.current_tenant_id') ?? auth()->user()?->tenant_id;
            }
        });
    }

    public function getTenantColumn(): string
    {
        return 'tenant_id';
    }
}