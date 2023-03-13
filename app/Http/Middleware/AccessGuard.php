<?php

namespace App\Http\Middleware;

use Closure;

class AccessGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $userdata = session("user_data");
        if (!$userdata) {
            return redirect()->route("login")->with([
                "message" => "Unauthorize access!"
            ]);
        }
        return $next($request);
    }
}
