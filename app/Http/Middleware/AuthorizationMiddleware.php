<?php

namespace App\Http\Middleware;

use App\Models\Group;
use App\Models\User_Group;
use Closure;
use Illuminate\Support\Facades\Auth;

class AuthorizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $role)
    {
        $user = Auth::user();
        if ($role === 'super_admin' && !$user->isAdmin()) {
            abort(403, 'Access denied: Not a super admin.');
        }
        if ($role === 'group_admin' && !$this->isGroupAdmin($request->input('group_id'), $user->id)) {
            abort(403, 'Access denied: Not a group admin.');
        }

        if ($role === 'group_member') {
            if (!$this->isGroupMember($request->input('group_id'), $user->id)) {
                abort(403, 'Access denied: Not a group member.');
            }
        }

        return $next($request);
    }

    protected function isGroupAdmin($groupId, $userId)
    {
        return Group::query()->where('id', $groupId)
            ->where('admin_id', $userId)
            ->exists();
    }

    protected function isGroupMember($groupId, $userId)
    {
        return User_Group::query()->where('group_id', $groupId)
            ->where('user_id', $userId)
            ->exists();
    }
}
