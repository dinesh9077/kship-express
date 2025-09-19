<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Permission
{ 
    public function handle(Request $request, Closure $next, $permission = null)
    {
        if (Auth::check())
		{ 
			$admin = Auth::user();
			$role = $admin->role;

			// If a specific permission is required
			if (!empty($permission)) {
				$hasPermission = $admin->rolePermissions()->where('name', $permission)->exists();
				if (!in_array($role, ['admin', 'user']) && !$hasPermission) {
					return abort(403);
				}
			}

			// If the user is not an admin, assign their role permissions
			if (!in_array($role, ['admin', 'user'])) 
			{ 
				$permissions = $admin->rolePermissions()->get(['name', 'value']);
				foreach ($permissions as $perm) {
					Config::set('permission.' . $perm->name, $perm->value);
				}
			} else {
				// If admin, grant full access permissions
				$permissions = DB::table('permissions')->where('status', 1)->pluck('name');
				$actions = ['view', 'add', 'edit', 'delete'];

				foreach ($permissions as $permName) {
					foreach ($actions as $action) {
						Config::set("permission.{$permName}.{$action}", true);
					}
				}
			}
		}
        return $next($request);
    }
}
