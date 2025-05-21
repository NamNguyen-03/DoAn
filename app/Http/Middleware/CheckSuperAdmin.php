<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Lấy thông tin admin từ guard 'admins'
        $admin = auth('admins')->user();
        // Kiểm tra xem admin có tồn tại và có vai trò 'superadmin' không
        if (!$admin instanceof \App\Models\Admin || !$admin->hasRole('superadmin')) {
            return response()->json(['message' => 'Bạn không có quyền thực hiện hành động này.'], 403);
        }

        return $next($request);
    }
}
