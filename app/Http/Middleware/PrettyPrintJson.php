<?php

namespace App\Http\Middleware;

use Closure;

class PrettyPrintJson
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
        $response = $next($request);
        // if ($response instanceof JsonResponse) {
            // if ($request->query(self::QUERY_PARAMETER) == 'true') {
                $response->setEncodingOptions(JSON_UNESCAPED_SLASHES);
            // }
        // }
        return $response;
    }
}
