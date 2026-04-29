<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;
use App\Models\CashierShift;

class CheckShift
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $storeSettings = Setting::getStoreSettings();

        if ($storeSettings->enable_shift) {
            $activeShift = CashierShift::where('user_id', auth()->id())
                ->where('status', 'open')
                ->first();

            if (!$activeShift) {
                // Jangan loop jika rute yang dituju sudah shift.create
                if (!$request->routeIs('pos.shift.create') && !$request->routeIs('pos.shift.store')) {
                    return redirect()->route('pos.shift.create');
                }
            }
        }

        return $next($request);
    }
}
