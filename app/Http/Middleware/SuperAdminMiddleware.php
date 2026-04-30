<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * Grants access ONLY to Superadmin users (role=admin AND branch_id=null).
     * Branch Admins (role=admin WITH branch_id set) are denied access to
     * system-level configurations like branches, warehouses, and global settings.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses ditolak. Fitur ini hanya tersedia untuk Superadmin.');
        }

        return $next($request);
    }
}
