<?php

namespace App\Listeners;

use App\Events\TenantRegister;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CreateTenantDatabase implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TenantRegister $event): void
    {
        $tenant = $event->tenant;
        $dbName = "tenant_{$tenant->id}";
        $tenant->database_options = [
            'dbname' => $dbName,
        ];
        DB::statement("CREATE DATABASE `{$dbName}`");

        $oldDbConnection =  config('database.connections.tenant.database');
        config(['database.connections.tenant.database' => $dbName]);

        // Connection Caching: Laravel caches database connections. Even if you change the configuration, the existing mysql connection instance remains active and connected to the old database. You must call DB::purge('mysql') to disconnect and force Laravel to create a new connection with the updated configuration.
        DB::purge('tenant');

        Artisan::call("migrate", [
            "--path" => "database/migrations/tenants",
            "--force" => true,
        ]);

        config(['database.connections.tenant.database' => $oldDbConnection]);
        DB::purge('tenant');
        $tenant->save();
    }
}
