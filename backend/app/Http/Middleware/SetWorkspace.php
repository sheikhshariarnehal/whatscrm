<?php

namespace App\Http\Middleware;

use App\Models\Workspace;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetWorkspace
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            $workspaceId = null;

            // 1. Check custom header (useful for API)
            if ($request->hasHeader('X-Workspace-Id')) {
                $workspaceId = (int) $request->header('X-Workspace-Id');
            }
            // 2. Check session
            elseif (session()->has('current_workspace_id')) {
                $workspaceId = (int) session('current_workspace_id');
            }

            // Verify membership or find first accessible workspace
            $workspace = null;
            if ($workspaceId) {
                $workspace = $user->workspaces()->where('workspaces.id', $workspaceId)->where('workspace_members.is_active', true)->first();
            }

            if (! $workspace) {
                $workspace = $user->workspaces()->where('workspace_members.is_active', true)->first();
            }

            if ($workspace) {
                session(['current_workspace_id' => $workspace->id]);
                App::instance('current_workspace_id', $workspace->id);
                App::instance('current_workspace', $workspace);

                // Share globally with Blade views
                View::share('currentWorkspace', $workspace);
                View::share('userWorkspaces', $user->workspaces()->where('workspace_members.is_active', true)->get());
            } else {
                App::forgetInstance('current_workspace_id');
                App::forgetInstance('current_workspace');
            }
        }

        return $next($request);
    }
}
