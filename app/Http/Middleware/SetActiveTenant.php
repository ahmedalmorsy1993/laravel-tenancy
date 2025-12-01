<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SetActiveTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $domain = explode('.', $host)[0];
        $tenant = Tenant::where('domain', $domain)->first();
        if ($tenant) {
            app()->instance('tenant', $tenant);
            if ($tenant->database_options['dbname']) {
                config(['database.connections.tenant.database' => $tenant->database_options['dbname']]);
            }
        }
        return $next($request);
    }
}
