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

        $oldDbConnection =  config('database.connections.mysql.database');
        config(['database.connections.mysql.database' => $dbName]);
        DB::purge('mysql');

        Artisan::call("migrate", [
            "--path" => "database/migrations/tenants",
            "--force" => true,
        ]);

        config(['database.connections.mysql.database' => $oldDbConnection]);
        DB::purge('mysql');
        $tenant->save();
    }
}
