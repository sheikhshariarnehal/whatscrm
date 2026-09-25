<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\App;

class WorkspaceScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (App::bound('current_workspace_id')) {
            $workspaceId = App::make('current_workspace_id');
            if ($workspaceId) {
                $builder->where($model->getTable() . '.workspace_id', $workspaceId);
            }
        }
    }
}
