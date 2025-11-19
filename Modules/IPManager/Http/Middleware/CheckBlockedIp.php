<?php

// Modules/SmartIpier/Http/Middleware/CheckBlockedIp.php

namespace Modules\IPManager\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckBlockedIp
{
    public function handle($request, Closure $next)
    {

        if (!isActiveModule('ipmanager')) {
            return $next($request);
        }

        if (!session()->has('impersonated_name') && isBlockedIP(Auth::user()?->email)) {
            Auth::logout();
            return redirect()->route('login');
        }

        return $next($request);
    }
}
