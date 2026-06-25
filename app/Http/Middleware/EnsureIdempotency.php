<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdempotency
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('Idempotency-Key');

        if (! $key) {
            return $next($request);
        }

        $route = $request->route()?->getName() ?? $request->path();

        $existing = DB::table('idempotency_keys')
            ->where('key', $key)
            ->where('route', $route)
            ->first();

        if ($existing) {
            return response()->json(
                json_decode($existing->response, true),
                $existing->status_code
            );
        }

        $response = $next($request);

        if ($response->isSuccessful()) {
            $inserted = DB::table('idempotency_keys')->insertOrIgnore([
                'key' => $key,
                'route' => $route,
                'response' => $response->getContent(),
                'status_code' => $response->getStatusCode(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($inserted === 0) {
                $existing = DB::table('idempotency_keys')
                    ->where('key', $key)
                    ->where('route', $route)
                    ->first();

                return response()->json(
                    json_decode($existing->response, true),
                    $existing->status_code
                );
            }
        }

        return $response;
    }
}
