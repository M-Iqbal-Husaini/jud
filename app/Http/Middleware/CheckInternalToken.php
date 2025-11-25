<?php
namespace App\Http\Middleware;
use Closure;

class CheckInternalToken
{
    public function handle($request, Closure $next)
    {
        $token = $request->header('X-INTERNAL-TOKEN');
        if (!$token || $token !== env('INTERNAL_API_TOKEN')) {
            return response()->json(['message'=>'Unauthorized'], 401);
        }
        return $next($request);
    }
}
