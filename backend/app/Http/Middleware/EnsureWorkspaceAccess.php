<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class EnsureWorkspaceAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?string $minRole = null): Response
    {
        if (! App::bound('current_workspace_id') || ! App::make('current_workspace_id')) {
            return redirect()->route('workspace.create')->with('info', 'Please create or join a workspace first.');
        }

        if ($minRole) {
            $user = $request->user();
            $workspaceId = App::make('current_workspace_id');
            $member = $user->workspaceMemberships()->where('workspace_id', $workspaceId)->first();

            if (! $member || ! $member->is_active) {
                abort(403, 'Unauthorized workspace access.');
            }

            if ($minRole === 'owner' && ! $member->isOwner()) {
                abort(403, 'Only the workspace owner can access this section.');
            }

            if ($minRole === 'admin' && ! $member->isAdmin()) {
                abort(403, 'Administrator privileges required.');
            }
        }

        return $next($request);
    }
}
