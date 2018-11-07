<?php

namespace App\Http\Middleware;

use Closure;
use App\User;


class ApiTokenCheck
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
        $token = $request->get('token', $request->bearerToken());
        $user = User::where('api_token', $token)->first();
        if (! $user) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'INVALID_OR_NO_TOKEN',
            ]);
        }

        return $next($request);
    }
}
