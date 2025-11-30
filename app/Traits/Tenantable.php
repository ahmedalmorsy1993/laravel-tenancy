<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Tenantable
{

    // if we want to call this trait when any model use this trait there is way laravel provide by attach boot before the trait name and laravel will call this trait function when initialize the model
    // in this case we can override booted method and handle what we want to do when initialize the model
    // because sometime we need booted method to listen to events or initialize something
    protected static function bootTenantable()
    {
        static::addGlobalScope('tenant', function (Builder $query) {
            $query->where('tenant_id', app()->make('tenant')->id);
        });
    }
}
