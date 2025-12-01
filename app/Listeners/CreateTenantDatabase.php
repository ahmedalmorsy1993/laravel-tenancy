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
        DB::statement("CREATE DATABASE `store_{$tenant->id}`");
        Artisan::call("migrate", [
            "--path" => "database/migrations/tenants",
            "--database" => "store_{$tenant->id}",
            "--force" => true,
        ]);
    }
}
