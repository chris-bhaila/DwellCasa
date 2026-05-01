<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class LocationScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (!Auth::check()) {
            return;
        }

        // Don't apply on public web routes
        if (!request()->is('admin*') && !request()->is('api*')) {
            return;
        }

        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            if (session('selected_location_id')) {
                $builder->where($model->getTable() . '.location_id', session('selected_location_id'));
            }
            return;
        }

        if ($user->location_id) {
            $builder->where($model->getTable() . '.location_id', $user->location_id);
        }
    }
}
