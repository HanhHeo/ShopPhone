<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user == "") {
            return redirect()->route('auth.login');
        } else {
            if ($user->level == 'Giám đốc') {
                return $next($request);
            } else {
                return redirect()->route('auth.login');
            }
        }
        return $next($request);
    }
}
